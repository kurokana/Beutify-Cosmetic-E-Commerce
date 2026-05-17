<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    /**
     * Show the payment page for the given order.
     * Requirements: 5.2
     *
     * GET /payment/{order}
     */
    public function show(Order $order): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status !== 'pending_payment') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Pesanan ini tidak memerlukan pembayaran.');
        }

        $syncedStatus = $this->paymentService->syncPaymentStatus($order);

        if ($syncedStatus === 'success') {
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pembayaran berhasil terdeteksi.');
        }

        if (in_array($syncedStatus, ['expired', 'failed', 'cancelled'], true)) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Pembayaran tidak berhasil. Silakan lakukan pembayaran ulang.');
        }

        $order->load(['items', 'address', 'payment', 'voucher']);

        return view('customer.payment.show', compact('order'));
    }

    /**
     * Create a Midtrans Snap transaction for the given order and return the
     * snap_token as JSON so the frontend can open the Snap popup.
     * Requirements: 5.1, 5.2
     *
     * POST /payment/create/{order}
     */
    public function create(Order $order): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pesanan ini.',
            ], 403);
        }

        if ($order->status !== 'pending_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak memerlukan pembayaran.',
            ], 422);
        }

        try {
            $snapToken = $this->paymentService->createSnapTransaction($order, false);

            return response()->json([
                'success'    => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Refresh Snap token to allow changing payment method.
     *
     * POST /payment/refresh/{order}
     */
    public function refresh(Order $order): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pesanan ini.',
            ], 403);
        }

        if ($order->status !== 'pending_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak memerlukan pembayaran.',
            ], 422);
        }

        try {
            $snapToken = $this->paymentService->createSnapTransaction($order, true);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui metode pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }
}

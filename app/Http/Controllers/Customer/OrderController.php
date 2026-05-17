<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private ShippingService $shippingService)
    {
    }

    /**
     * Display a listing of the authenticated user's orders.
     * Requirements: 5.9
     *
     * GET /orders
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with(['payment'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order detail.
     * Requirements: 5.9
     *
     * GET /orders/{order}
     */
    public function show(Order $order): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['items.product.images', 'address', 'payment', 'voucher']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Confirm receipt of the order (changes status to delivered).
     * Requirements: 6.6 — stub for Task 15.2
     *
     * PATCH /orders/{order}/confirm
     */
    public function confirm(Order $order): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan tidak dapat dikonfirmasi pada status saat ini.');
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Penerimaan pesanan berhasil dikonfirmasi. Terima kasih!');
    }

    /**
     * Request a refund within 24 hours after confirming receipt.
     *
     * POST /orders/{order}/refund
     */
    public function requestRefund(Request $request, Order $order): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Refund hanya bisa diajukan untuk pesanan yang sudah selesai.');
        }

        if ($order->refund_requested_at) {
            return back()->with('error', 'Refund untuk pesanan ini sudah diajukan.');
        }

        if (! $order->delivered_at) {
            $order->update([
                'delivered_at' => $order->updated_at ?? now(),
            ]);
            $order->refresh();
        }

        if (! $order->delivered_at) {
            return back()->with('error', 'Waktu penyelesaian pesanan tidak ditemukan.');
        }

        if ($order->delivered_at->copy()->addDay()->isPast()) {
            return back()->with('error', 'Masa pengajuan refund (1x24 jam) sudah berakhir.');
        }

        if ($order->payment?->status !== 'success') {
            return back()->with('error', 'Refund hanya tersedia untuk pesanan yang sudah dibayar.');
        }

        $data = $request->validate([
            'refund_reason' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $order->loadMissing(['items', 'payment']);

            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update([
                'status' => 'cancelled',
                'refund_requested_at' => now(),
                'refund_reason' => $data['refund_reason'] ?? null,
            ]);
        });

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Permintaan refund berhasil diajukan. Tim kami akan memprosesnya.');
    }

    /**
     * Track the shipment of an order via RajaOngkir.
     * Requirements: 6.4, 6.5
     *
     * GET /orders/{order}/track
     */
    public function track(Order $order): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (! $order->shipping_tracking_number) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Nomor resi belum tersedia untuk pesanan ini.');
        }

        $tracking     = null;
        $trackingError = null;

        try {
            $tracking = $this->shippingService->trackShipment(
                $order->shipping_tracking_number,
                $order->courier_name
            );
        } catch (\Exception $e) {
            $trackingError = 'Informasi pelacakan belum tersedia, silakan coba beberapa saat lagi';
        }

        return view('customer.orders.track', compact('order', 'tracking', 'trackingError'));
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $order->update(['status' => 'delivered']);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Penerimaan pesanan berhasil dikonfirmasi. Terima kasih!');
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

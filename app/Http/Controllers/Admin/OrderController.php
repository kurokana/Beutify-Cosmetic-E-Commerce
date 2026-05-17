<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderStatusUpdateJob;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders with filtering capabilities.
     * Requirements: 11.1
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'payment'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by customer name
        if ($request->filled('customer_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get all unique statuses for filter dropdown
        $statuses = [
            'pending_payment' => 'Menunggu Pembayaran',
            'payment_confirmed' => 'Pembayaran Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Sedang Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Display the specified order with full details.
     * Requirements: 11.2
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'address',
            'items.product',
            'items.variant',
            'payment',
            'voucher',
        ]);

        // Get status history (we'll use created_at and updated_at as simple history)
        // In a more complex system, you'd have a separate order_status_history table
        $statusHistory = [
            [
                'status' => $order->status,
                'timestamp' => $order->updated_at,
            ],
        ];

        return view('admin.orders.show', compact('order', 'statusHistory'));
    }

    /**
     * Update the status of the specified order.
     * Requirements: 11.3
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending_payment,payment_confirmed,processing,shipped,delivered,cancelled'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'delivered' && ! $order->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        // Update the order status
        $order->update($updateData);

        // Dispatch email notification job when status changes
        if ($oldStatus !== $newStatus) {
            SendOrderStatusUpdateJob::dispatch($order, $oldStatus, $newStatus);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Update the tracking number and set status to shipped.
     * Requirements: 11.4, 6.3
     */
    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $oldStatus = $order->status;

        // Update tracking number and status
        $order->update([
            'shipping_tracking_number' => $validated['shipping_tracking_number'],
            'status' => 'shipped',
        ]);

        // Dispatch email notification job
        if ($oldStatus !== 'shipped') {
            SendOrderStatusUpdateJob::dispatch($order, $oldStatus, 'shipped');
        }

        return back()->with('success', 'Nomor resi berhasil disimpan dan status pesanan diperbarui ke "Sedang Dikirim".');
    }
}

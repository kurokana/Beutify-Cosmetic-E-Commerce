<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics.
     *
     * Shows total products, total orders, total users, total revenue,
     * weekly sales, recent orders, and recent admin activity.
     */
    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders'   => Order::count(),
            'total_users'    => User::where('role', 'customer')->count(),
            'total_revenue'  => Order::whereIn('status', [
                'payment_confirmed',
                'processing',
                'shipped',
                'delivered',
            ])->sum('total_amount'),
        ];

        $salesStatuses = [
            'payment_confirmed',
            'processing',
            'shipped',
            'delivered',
        ];

        $dailySales = [
            'labels'  => [],
            'orders'  => [],
            'revenue' => [],
        ];

        for ($days = 6; $days >= 0; $days--) {
            $date = Carbon::today()->subDays($days);

            $dailySales['labels'][] = $date->format('d M');
            $dailySales['orders'][] = Order::whereIn('status', $salesStatuses)
                ->whereDate('created_at', $date)
                ->count();
            $dailySales['revenue'][] = Order::whereIn('status', $salesStatuses)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
        }

        $recentOrders = Order::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'dailySales', 'recentOrders'));
    }
}

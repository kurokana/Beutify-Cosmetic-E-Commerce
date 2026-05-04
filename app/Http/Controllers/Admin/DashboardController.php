<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics.
     *
     * Shows total products, total orders, total users, and total revenue
     * to give the admin a quick overview of the store's performance.
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

        return view('admin.dashboard', compact('stats'));
    }
}

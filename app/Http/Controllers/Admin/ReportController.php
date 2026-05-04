<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display sales reports with daily, weekly, and monthly summaries.
     * Requirements: 11.5
     */
    public function index(Request $request): View
    {
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Daily sales report
        $dailySales = $this->getDailySales($startDate, $endDate);

        // Weekly sales report
        $weeklySales = $this->getWeeklySales($startDate, $endDate);

        // Monthly sales report
        $monthlySales = $this->getMonthlySales($startDate, $endDate);

        // Best-selling products
        $bestSellingProducts = $this->getBestSellingProducts($startDate, $endDate);

        // Summary statistics
        $summary = $this->getSummaryStatistics($startDate, $endDate);

        return view('admin.reports.index', compact(
            'dailySales',
            'weeklySales',
            'monthlySales',
            'bestSellingProducts',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get daily sales aggregation.
     */
    private function getDailySales(string $startDate, string $endDate): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $dateSelect = "strftime('%Y-%m-%d', created_at)";
        } else {
            $dateSelect = 'DATE(created_at)';
        }

        $results = Order::select(
            DB::raw("{$dateSelect} as date"),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->whereIn('status', [
                'payment_confirmed',
                'processing',
                'shipped',
                'delivered',
            ])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'labels' => $results->pluck('date')->map(fn($date) => date('d M Y', strtotime($date)))->toArray(),
            'orders' => $results->pluck('total_orders')->toArray(),
            'revenue' => $results->pluck('total_revenue')->toArray(),
            'data' => $results->toArray(),
        ];
    }

    /**
     * Get weekly sales aggregation.
     */
    private function getWeeklySales(string $startDate, string $endDate): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $yearSelect = "strftime('%Y', created_at)";
            $weekSelect = "strftime('%W', created_at)";
            $weekStartSelect = "MIN(strftime('%Y-%m-%d', created_at))";
            $weekEndSelect = "MAX(strftime('%Y-%m-%d', created_at))";
        } else {
            $yearSelect = 'YEAR(created_at)';
            $weekSelect = 'WEEK(created_at, 1)';
            $weekStartSelect = 'MIN(DATE(created_at))';
            $weekEndSelect = 'MAX(DATE(created_at))';
        }

        $results = Order::select(
            DB::raw("{$yearSelect} as year"),
            DB::raw("{$weekSelect} as week"),
            DB::raw("{$weekStartSelect} as week_start"),
            DB::raw("{$weekEndSelect} as week_end"),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->whereIn('status', [
                'payment_confirmed',
                'processing',
                'shipped',
                'delivered',
            ])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        return [
            'labels' => $results->map(function ($item) {
                return 'Week ' . $item->week . ' (' . date('d M', strtotime($item->week_start)) . ' - ' . date('d M', strtotime($item->week_end)) . ')';
            })->toArray(),
            'orders' => $results->pluck('total_orders')->toArray(),
            'revenue' => $results->pluck('total_revenue')->toArray(),
            'data' => $results->toArray(),
        ];
    }

    /**
     * Get monthly sales aggregation.
     */
    private function getMonthlySales(string $startDate, string $endDate): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $yearSelect = "strftime('%Y', created_at)";
            $monthSelect = "strftime('%m', created_at)";
        } else {
            $yearSelect = 'YEAR(created_at)';
            $monthSelect = 'MONTH(created_at)';
        }

        $results = Order::select(
            DB::raw("{$yearSelect} as year"),
            DB::raw("{$monthSelect} as month"),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->whereIn('status', [
                'payment_confirmed',
                'processing',
                'shipped',
                'delivered',
            ])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return [
            'labels' => $results->map(function ($item) {
                return date('M Y', strtotime($item->year . '-' . $item->month . '-01'));
            })->toArray(),
            'orders' => $results->pluck('total_orders')->toArray(),
            'revenue' => $results->pluck('total_revenue')->toArray(),
            'data' => $results->toArray(),
        ];
    }

    /**
     * Get best-selling products.
     */
    private function getBestSellingProducts(string $startDate, string $endDate, int $limit = 10): array
    {
        $results = OrderItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', [
                    'payment_confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                ])
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();

        return $results->toArray();
    }

    /**
     * Get summary statistics for the date range.
     */
    private function getSummaryStatistics(string $startDate, string $endDate): array
    {
        $orders = Order::whereIn('status', [
            'payment_confirmed',
            'processing',
            'shipped',
            'delivered',
        ])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'total_products_sold' => OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', [
                    'payment_confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                ])
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })->sum('quantity'),
        ];
    }
}

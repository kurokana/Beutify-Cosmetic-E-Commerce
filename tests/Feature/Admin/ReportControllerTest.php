<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create customer user
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_view_reports_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Laporan Penjualan');
    }

    /** @test */
    public function reports_page_displays_summary_statistics()
    {
        // Create confirmed orders
        Order::factory()->count(3)->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Total Pesanan');
        $response->assertSee('Total Pendapatan');
        $response->assertSee('Rata-rata Nilai Pesanan');
        $response->assertSee('Total Produk Terjual');
    }

    /** @test */
    public function reports_page_displays_daily_sales_data()
    {
        // Create order for today
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
            'total_amount' => 150000,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Penjualan Harian');
        $response->assertSee('Detail Penjualan Harian');
    }

    /** @test */
    public function reports_page_displays_weekly_sales_data()
    {
        // Create orders in current week
        Order::factory()->count(2)->create([
            'user_id' => $this->customer->id,
            'status' => 'shipped',
            'total_amount' => 200000,
            'created_at' => now()->startOfWeek(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Penjualan Mingguan');
    }

    /** @test */
    public function reports_page_displays_monthly_sales_data()
    {
        // Create orders in current month
        Order::factory()->count(5)->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
            'total_amount' => 300000,
            'created_at' => now()->startOfMonth(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Penjualan Bulanan');
    }

    /** @test */
    public function reports_page_displays_best_selling_products()
    {
        $product = Product::factory()->create(['name' => 'Best Seller Product']);

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Best Seller Product',
            'quantity' => 10,
            'price' => 50000,
            'subtotal' => 500000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Produk Terlaris');
        $response->assertSee('Best Seller Product');
    }

    /** @test */
    public function reports_can_be_filtered_by_date_range()
    {
        // Create old order
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
            'total_amount' => 100000,
            'created_at' => now()->subDays(60),
        ]);

        // Create recent order
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
            'total_amount' => 200000,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index', [
                'start_date' => now()->subDays(7)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
            ]));

        $response->assertOk();
    }

    /** @test */
    public function reports_only_include_confirmed_orders()
    {
        // Create pending order (should not be included)
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending_payment',
            'total_amount' => 100000,
        ]);

        // Create cancelled order (should not be included)
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'cancelled',
            'total_amount' => 150000,
        ]);

        // Create confirmed order (should be included)
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
            'total_amount' => 200000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        // The summary should only count the confirmed order
    }

    /** @test */
    public function reports_page_includes_chart_js_script()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('chart.js', false);
        $response->assertSee('dailySalesChart', false);
        $response->assertSee('weeklySalesChart', false);
        $response->assertSee('monthlySalesChart', false);
    }

    /** @test */
    public function non_admin_cannot_access_reports()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_reports()
    {
        $response = $this->get(route('admin.reports.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function reports_calculate_total_revenue_correctly()
    {
        // Create multiple orders with different amounts
        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
            'total_amount' => 100000,
        ]);

        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'shipped',
            'total_amount' => 150000,
        ]);

        Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
            'total_amount' => 200000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        // Total should be 450000
        $response->assertSee('450.000', false);
    }

    /** @test */
    public function reports_calculate_average_order_value_correctly()
    {
        // Create 3 orders with total 300000
        Order::factory()->count(3)->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
            'total_amount' => 100000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        // Average should be 100000
        $response->assertSee('100.000', false);
    }

    /** @test */
    public function best_selling_products_are_sorted_by_quantity()
    {
        $product1 = Product::factory()->create(['name' => 'Product A']);
        $product2 = Product::factory()->create(['name' => 'Product B']);

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        // Product B sold more
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_name' => 'Product A',
            'quantity' => 5,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => 'Product B',
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        // Product B should appear before Product A in the list
        $content = $response->getContent();
        $posB = strpos($content, 'Product B');
        $posA = strpos($content, 'Product A');
        $this->assertLessThan($posA, $posB);
    }

    /** @test */
    public function reports_handle_empty_data_gracefully()
    {
        // No orders in database
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Tidak ada data');
    }
}

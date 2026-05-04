<?php

namespace Tests\Feature\Admin;

use App\Jobs\SendOrderStatusUpdateJob;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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
    public function admin_can_view_orders_index()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-20240101-00001',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('ORD-20240101-00001');
    }

    /** @test */
    public function orders_index_displays_customer_information()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee($this->customer->name);
        $response->assertSee($this->customer->email);
    }

    /** @test */
    public function admin_can_filter_orders_by_status()
    {
        $pendingOrder = Order::factory()->create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-20240101-00001',
            'status' => 'pending_payment',
        ]);

        $shippedOrder = Order::factory()->shipped()->create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-20240101-00002',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['status' => 'shipped']));

        $response->assertOk();
        $response->assertSee('ORD-20240101-00002');
        $response->assertDontSee('ORD-20240101-00001');
    }

    /** @test */
    public function admin_can_filter_orders_by_date_range()
    {
        $oldOrder = Order::factory()->create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-20240101-00001',
            'created_at' => now()->subDays(10),
        ]);

        $recentOrder = Order::factory()->create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-20240101-00002',
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', [
                'date_from' => now()->subDays(3)->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ]));

        $response->assertOk();
        $response->assertSee('ORD-20240101-00002');
        $response->assertDontSee('ORD-20240101-00001');
    }

    /** @test */
    public function admin_can_filter_orders_by_customer_name()
    {
        $customer1 = User::factory()->create(['name' => 'John Doe']);
        $customer2 = User::factory()->create(['name' => 'Jane Smith']);

        $order1 = Order::factory()->create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-20240101-00001',
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $customer2->id,
            'order_number' => 'ORD-20240101-00002',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['customer_name' => 'John']));

        $response->assertOk();
        $response->assertSee('ORD-20240101-00001');
        $response->assertDontSee('ORD-20240101-00002');
    }

    /** @test */
    public function admin_can_view_order_details()
    {
        $address = Address::factory()->create([
            'user_id' => $this->customer->id,
            'recipient_name' => 'John Doe',
            'phone' => '081234567890',
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'address_id' => $address->id,
            'order_number' => 'ORD-20240101-00001',
        ]);

        $product = Product::factory()->create(['name' => 'Test Product']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Test Product',
            'quantity' => 2,
            'price' => 50000,
            'subtotal' => 100000,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'success',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('ORD-20240101-00001');
        $response->assertSee('Test Product');
        $response->assertSee('John Doe');
        $response->assertSee('081234567890');
    }

    /** @test */
    public function order_details_displays_all_order_items()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        // Create payment to avoid null reference in view
        Payment::factory()->create([
            'order_id' => $order->id,
        ]);

        $product1 = Product::factory()->create(['name' => 'Product One']);
        $product2 = Product::factory()->create(['name' => 'Product Two']);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_name' => 'Product One',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => 'Product Two',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('Product One');
        $response->assertSee('Product Two');
    }

    /** @test */
    public function order_details_displays_payment_information()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'payment_method' => 'BCA Virtual Account',
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('BCA Virtual Account');
        $response->assertSee('Berhasil');
    }

    /** @test */
    public function order_details_displays_shipping_information()
    {
        $order = Order::factory()->shipped()->create([
            'user_id' => $this->customer->id,
            'courier_name' => 'jne',
            'courier_service' => 'REG',
            'shipping_tracking_number' => 'JNE1234567890',
        ]);

        // Create payment to avoid null reference in view
        Payment::factory()->create([
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('JNE');
        $response->assertSee('REG');
        $response->assertSee('JNE1234567890');
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        Queue::fake();

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'processing',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    /** @test */
    public function updating_order_status_dispatches_email_notification()
    {
        Queue::fake();

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'processing',
            ]);

        Queue::assertPushed(SendOrderStatusUpdateJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id
                && $job->oldStatus === 'payment_confirmed'
                && $job->newStatus === 'processing';
        });
    }

    /** @test */
    public function updating_to_same_status_does_not_dispatch_email()
    {
        Queue::fake();

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'processing',
            ]);

        Queue::assertNotPushed(SendOrderStatusUpdateJob::class);
    }

    /** @test */
    public function order_status_update_requires_valid_status()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'payment_confirmed',
        ]);
    }

    /** @test */
    public function admin_can_update_tracking_number()
    {
        Queue::fake();

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
            'shipping_tracking_number' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => 'JNE1234567890',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_tracking_number' => 'JNE1234567890',
            'status' => 'shipped',
        ]);
    }

    /** @test */
    public function updating_tracking_number_changes_status_to_shipped()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => 'JNE1234567890',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);
    }

    /** @test */
    public function updating_tracking_number_dispatches_email_notification()
    {
        Queue::fake();

        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => 'JNE1234567890',
            ]);

        Queue::assertPushed(SendOrderStatusUpdateJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id
                && $job->oldStatus === 'processing'
                && $job->newStatus === 'shipped';
        });
    }

    /** @test */
    public function tracking_number_is_required()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => '',
            ]);

        $response->assertSessionHasErrors('shipping_tracking_number');
    }

    /** @test */
    public function tracking_number_cannot_exceed_max_length()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => str_repeat('A', 101),
            ]);

        $response->assertSessionHasErrors('shipping_tracking_number');
    }

    /** @test */
    public function non_admin_cannot_access_orders_index()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.orders.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function non_admin_cannot_view_order_details()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('admin.orders.show', $order));

        $response->assertForbidden();
    }

    /** @test */
    public function non_admin_cannot_update_order_status()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'payment_confirmed',
        ]);

        $response = $this->actingAs($this->customer)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'processing',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'payment_confirmed',
        ]);
    }

    /** @test */
    public function non_admin_cannot_update_tracking_number()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($this->customer)
            ->patch(route('admin.orders.update-tracking', $order), [
                'shipping_tracking_number' => 'JNE1234567890',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_tracking_number' => null,
        ]);
    }

    /** @test */
    public function guest_cannot_access_orders_index()
    {
        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_view_order_details()
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        $response = $this->get(route('admin.orders.show', $order));

        $response->assertRedirect(route('login'));
    }
}

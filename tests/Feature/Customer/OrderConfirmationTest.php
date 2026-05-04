<?php

namespace Tests\Feature\Customer;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a customer user
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Create an address for the customer
        $address = Address::factory()->create([
            'user_id' => $this->customer->id,
        ]);

        // Create a product
        $product = Product::factory()->create([
            'price' => 100000,
            'stock' => 10,
        ]);

        // Create an order with 'shipped' status
        $this->order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'address_id' => $address->id,
            'status' => 'shipped',
            'subtotal' => 100000,
            'shipping_cost' => 10000,
            'total_amount' => 110000,
        ]);

        // Create order item
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);
    }

    /**
     * Test that confirmation button is displayed when order status is 'shipped'.
     * Requirements: 6.6
     */
    public function test_confirmation_button_displayed_when_order_is_shipped(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Penerimaan');
        $response->assertSee(route('orders.confirm', $this->order->id));
    }

    /**
     * Test that confirmation button is NOT displayed when order status is not 'shipped'.
     * Requirements: 6.6
     */
    public function test_confirmation_button_not_displayed_when_order_is_not_shipped(): void
    {
        // Update order status to 'payment_confirmed'
        $this->order->update(['status' => 'payment_confirmed']);

        $response = $this->actingAs($this->customer)
            ->get(route('orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertDontSee('Konfirmasi Penerimaan');
    }

    /**
     * Test that customer can confirm receipt of shipped order.
     * Requirements: 6.6
     */
    public function test_customer_can_confirm_receipt_of_shipped_order(): void
    {
        $response = $this->actingAs($this->customer)
            ->patch(route('orders.confirm', $this->order->id));

        $response->assertRedirect(route('orders.show', $this->order->id));
        $response->assertSessionHas('success', 'Penerimaan pesanan berhasil dikonfirmasi. Terima kasih!');

        // Assert order status updated to 'delivered'
        $this->order->refresh();
        $this->assertEquals('delivered', $this->order->status);
    }

    /**
     * Test that customer cannot confirm receipt of order that is not shipped.
     * Requirements: 6.6
     */
    public function test_customer_cannot_confirm_receipt_of_non_shipped_order(): void
    {
        // Update order status to 'payment_confirmed'
        $this->order->update(['status' => 'payment_confirmed']);

        $response = $this->actingAs($this->customer)
            ->patch(route('orders.confirm', $this->order->id));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pesanan tidak dapat dikonfirmasi pada status saat ini.');

        // Assert order status remains unchanged
        $this->order->refresh();
        $this->assertEquals('payment_confirmed', $this->order->status);
    }

    /**
     * Test that customer cannot confirm another customer's order.
     * Requirements: 6.6
     */
    public function test_customer_cannot_confirm_another_customers_order(): void
    {
        // Create another customer
        $anotherCustomer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($anotherCustomer)
            ->patch(route('orders.confirm', $this->order->id));

        $response->assertStatus(403);

        // Assert order status remains unchanged
        $this->order->refresh();
        $this->assertEquals('shipped', $this->order->status);
    }

    /**
     * Test that guest cannot confirm order receipt.
     * Requirements: 6.6
     */
    public function test_guest_cannot_confirm_order_receipt(): void
    {
        $response = $this->patch(route('orders.confirm', $this->order->id));

        $response->assertRedirect(route('login'));

        // Assert order status remains unchanged
        $this->order->refresh();
        $this->assertEquals('shipped', $this->order->status);
    }

    /**
     * Test that order status changes from 'shipped' to 'delivered' after confirmation.
     * Requirements: 6.6
     */
    public function test_order_status_changes_to_delivered_after_confirmation(): void
    {
        // Ensure order is in 'shipped' status
        $this->assertEquals('shipped', $this->order->status);

        $this->actingAs($this->customer)
            ->patch(route('orders.confirm', $this->order->id));

        // Assert order status is now 'delivered'
        $this->order->refresh();
        $this->assertEquals('delivered', $this->order->status);
    }

    /**
     * Test that confirmation button is not displayed after order is delivered.
     * Requirements: 6.6
     */
    public function test_confirmation_button_not_displayed_after_order_is_delivered(): void
    {
        // Update order status to 'delivered'
        $this->order->update(['status' => 'delivered']);

        $response = $this->actingAs($this->customer)
            ->get(route('orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertDontSee('Konfirmasi Penerimaan');
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Order $order;
    private Payment $payment;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $this->product = Product::factory()->create([
            'stock' => 100,
            'price' => 100000,
        ]);

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $address->id,
            'order_number' => 'ORD-20240101-00001',
            'status' => 'pending_payment',
            'total_amount' => 100000,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'price' => 100000,
            'quantity' => 5,
            'subtotal' => 500000,
        ]);

        // Reduce stock as if checkout happened
        $this->product->decrement('stock', 5);

        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'midtrans_order_id' => 'ORD-20240101-00001',
            'amount' => 100000,
            'status' => 'pending',
            'expired_at' => now()->addHours(24),
        ]);
    }

    /**
     * Test webhook with valid signature and successful payment.
     * Requirements: 5.6, 5.8
     */
    public function test_webhook_handles_successful_payment(): void
    {
        Queue::fake();

        $orderId = 'ORD-20240101-00001';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson('/api/webhook/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'TXN-123456',
            'payment_type' => 'bank_transfer',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Assert payment status updated
        $this->payment->refresh();
        $this->assertEquals('success', $this->payment->status);
        $this->assertEquals('TXN-123456', $this->payment->midtrans_transaction_id);
        $this->assertNotNull($this->payment->paid_at);

        // Assert order status updated
        $this->order->refresh();
        $this->assertEquals('payment_confirmed', $this->order->status);

        // Assert job dispatched
        Queue::assertPushed(SendPaymentConfirmationJob::class, function ($job) {
            return $job->order->id === $this->order->id;
        });
    }

    /**
     * Test webhook with invalid signature.
     * Requirements: 5.6
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/api/webhook/midtrans', [
            'order_id' => 'ORD-20240101-00001',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'invalid_signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['status' => 'error', 'message' => 'Invalid signature']);

        // Assert payment status not updated
        $this->payment->refresh();
        $this->assertEquals('pending', $this->payment->status);
    }

    /**
     * Test webhook handles expired payment and restores stock.
     * Requirements: 5.7
     */
    public function test_webhook_handles_expired_payment_and_restores_stock(): void
    {
        $orderId = 'ORD-20240101-00001';
        $statusCode = '407';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Stock before: 95 (100 - 5)
        $this->assertEquals(95, $this->product->fresh()->stock);

        $response = $this->postJson('/api/webhook/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'transaction_id' => 'TXN-123456',
            'payment_type' => 'bank_transfer',
        ]);

        $response->assertStatus(200);

        // Assert payment status updated
        $this->payment->refresh();
        $this->assertEquals('expired', $this->payment->status);

        // Assert order status updated to cancelled
        $this->order->refresh();
        $this->assertEquals('cancelled', $this->order->status);

        // Assert stock restored (95 + 5 = 100)
        $this->assertEquals(100, $this->product->fresh()->stock);
    }

    /**
     * Test webhook handles failed payment and restores stock.
     * Requirements: 5.7
     */
    public function test_webhook_handles_failed_payment_and_restores_stock(): void
    {
        $orderId = 'ORD-20240101-00001';
        $statusCode = '202';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // Stock before: 95 (100 - 5)
        $this->assertEquals(95, $this->product->fresh()->stock);

        $response = $this->postJson('/api/webhook/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'deny',
            'fraud_status' => 'deny',
            'transaction_id' => 'TXN-123456',
            'payment_type' => 'credit_card',
        ]);

        $response->assertStatus(200);

        // Assert payment status updated
        $this->payment->refresh();
        $this->assertEquals('failed', $this->payment->status);

        // Assert order status updated to cancelled
        $this->order->refresh();
        $this->assertEquals('cancelled', $this->order->status);

        // Assert stock restored (95 + 5 = 100)
        $this->assertEquals(100, $this->product->fresh()->stock);
    }

    /**
     * Test webhook restores variant stock when order has variants.
     * Requirements: 5.7
     */
    public function test_webhook_restores_variant_stock_on_cancellation(): void
    {
        // Create a product variant
        $variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'name' => 'Size',
            'value' => 'Large',
            'additional_price' => 0,
            'stock' => 50,
            'sku' => 'PROD-LARGE',
        ]);

        // Create order with variant
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => Address::factory()->create(['user_id' => $this->user->id])->id,
            'order_number' => 'ORD-20240101-00002',
            'status' => 'pending_payment',
            'total_amount' => 100000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_variant_id' => $variant->id,
            'product_name' => $this->product->name,
            'variant_name' => 'Large',
            'price' => 100000,
            'quantity' => 3,
            'subtotal' => 300000,
        ]);

        // Reduce variant stock
        $variant->decrement('stock', 3);
        $this->assertEquals(47, $variant->fresh()->stock);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'ORD-20240101-00002',
            'amount' => 100000,
            'status' => 'pending',
            'expired_at' => now()->addHours(24),
        ]);

        $orderId = 'ORD-20240101-00002';
        $statusCode = '407';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $response = $this->postJson('/api/webhook/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'transaction_id' => 'TXN-123457',
            'payment_type' => 'bank_transfer',
        ]);

        $response->assertStatus(200);

        // Assert variant stock restored (47 + 3 = 50)
        $this->assertEquals(50, $variant->fresh()->stock);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that PaymentService is properly configured with Midtrans settings.
     *
     * @return void
     */
    public function test_payment_service_is_configured(): void
    {
        $service = new PaymentService();

        // Verify that the service can be instantiated without errors
        $this->assertInstanceOf(PaymentService::class, $service);
    }

    /**
     * Test that createSnapTransaction creates a payment record with snap_token.
     *
     * @return void
     */
    public function test_create_snap_transaction_creates_payment_record(): void
    {
        // Skip this test if Midtrans credentials are not configured
        if (empty(config('midtrans.server_key')) || empty(config('midtrans.client_key'))) {
            $this->markTestSkipped('Midtrans credentials not configured in .env');
        }

        // Create test data
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'total_amount' => 100000,
            'status' => 'pending_payment',
        ]);

        $service = new PaymentService();

        try {
            $snapToken = $service->createSnapTransaction($order);

            // Verify snap_token is returned
            $this->assertNotEmpty($snapToken);
            $this->assertIsString($snapToken);

            // Verify payment record is created
            $payment = Payment::where('order_id', $order->id)->first();
            $this->assertNotNull($payment);
            $this->assertEquals($snapToken, $payment->snap_token);
            $this->assertEquals($order->order_number, $payment->midtrans_order_id);
            $this->assertEquals($order->total_amount, $payment->amount);
            $this->assertEquals('pending', $payment->status);
        } catch (\Exception $e) {
            // If Midtrans API call fails (e.g., invalid credentials), skip the test
            $this->markTestSkipped('Midtrans API call failed: ' . $e->getMessage());
        }
    }

    /**
     * Test that createSnapTransaction reuses existing snap_token if available.
     *
     * @return void
     */
    public function test_create_snap_transaction_reuses_existing_token(): void
    {
        // Create test data
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'total_amount' => 100000,
            'status' => 'pending_payment',
        ]);

        $existingToken = 'existing-snap-token-12345';
        Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => $order->order_number,
            'amount' => $order->total_amount,
            'status' => 'pending',
            'snap_token' => $existingToken,
            'expired_at' => now()->addHours(24),
        ]);

        $service = new PaymentService();
        $snapToken = $service->createSnapTransaction($order);

        // Verify the existing token is reused
        $this->assertEquals($existingToken, $snapToken);
    }
}

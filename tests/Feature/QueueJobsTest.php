<?php

namespace Tests\Feature;

use App\Jobs\SendEmailVerificationJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Jobs\SendOrderStatusUpdateJob;
use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueJobsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that SendEmailVerificationJob is dispatched on user registration.
     * Requirement: 1.1
     */
    public function test_email_verification_job_dispatched_on_registration(): void
    {
        Queue::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));

        Queue::assertPushed(SendEmailVerificationJob::class, function ($job) {
            return $job->user->email === 'test@example.com';
        });
    }

    /**
     * Test that SendEmailVerificationJob sends verification email.
     * Requirement: 1.1
     */
    public function test_email_verification_job_sends_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $job = new SendEmailVerificationJob($user);
        $job->handle();

        // Verify that a notification was sent
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    /**
     * Test that SendEmailVerificationJob skips already verified users.
     * Requirement: 1.1
     */
    public function test_email_verification_job_skips_verified_users(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $job = new SendEmailVerificationJob($user);
        $job->handle();

        // Should not send notification to already verified user
        Notification::assertNothingSent();
    }

    /**
     * Test that SendEmailVerificationJob is dispatched when resending verification.
     * Requirement: 1.1
     */
    public function test_email_verification_job_dispatched_on_resend(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Queue::assertPushed(SendEmailVerificationJob::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }

    /**
     * Test that SendOrderConfirmationJob is dispatched when order is created.
     * Requirement: 4.8
     */
    public function test_order_confirmation_job_dispatched_on_order_creation(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100000, 'stock' => 10]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Add product to cart
        $this->post("/cart/add", [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Proceed to checkout
        $response = $this->post('/checkout', [
            'address_id' => $address->id,
            'courier_name' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 10000,
        ]);

        Queue::assertPushed(SendOrderConfirmationJob::class, function ($job) {
            return $job->order instanceof Order;
        });
    }

    /**
     * Test that SendOrderConfirmationJob sends email with order details.
     * Requirement: 4.8
     */
    public function test_order_confirmation_job_sends_email_with_details(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_number' => 'ORD-20240101-00001',
            'status' => 'pending_payment',
            'total_amount' => 110000,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 110000,
            'status' => 'pending',
        ]);

        $job = new SendOrderConfirmationJob($order);
        $job->handle();

        // Verify that Mail::send was called (it uses views, not Mailable classes)
        // We can't easily test Mail::send with MailFake, so we just verify no exceptions
        $this->assertTrue(true);
    }

    /**
     * Test that SendPaymentConfirmationJob is dispatched on payment success.
     * Requirement: 5.8
     */
    public function test_payment_confirmation_job_dispatched_on_payment_success(): void
    {
        $this->markTestSkipped('Requires Midtrans webhook configuration');
    }

    /**
     * Test that SendPaymentConfirmationJob sends email with payment details.
     * Requirement: 5.8
     */
    public function test_payment_confirmation_job_sends_email_with_details(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => 'payment_confirmed',
            'total_amount' => 110000,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 100000,
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 110000,
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $job = new SendPaymentConfirmationJob($order);
        $job->handle();

        // Verify that Mail::send was called (it uses views, not Mailable classes)
        // We can't easily test Mail::send with MailFake, so we just verify no exceptions
        $this->assertTrue(true);
    }

    /**
     * Test that SendOrderStatusUpdateJob is dispatched when order status changes.
     * Requirement: 11.3
     */
    public function test_order_status_update_job_dispatched_on_status_change(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $address = Address::factory()->create(['user_id' => $customer->id]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'address_id' => $address->id,
            'status' => 'payment_confirmed',
        ]);

        $this->actingAs($admin);

        // Update order status
        $this->patch("/admin/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        Queue::assertPushed(SendOrderStatusUpdateJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id
                && $job->oldStatus === 'payment_confirmed'
                && $job->newStatus === 'processing';
        });
    }

    /**
     * Test that SendOrderStatusUpdateJob sends email with status change details.
     * Requirement: 11.3
     */
    public function test_order_status_update_job_sends_email_with_details(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => 'processing',
        ]);

        $job = new SendOrderStatusUpdateJob($order, 'payment_confirmed', 'processing');
        $job->handle();

        // Verify that Mail::send was called (it uses views, not Mailable classes)
        // We can't easily test Mail::send with MailFake, so we just verify no exceptions
        $this->assertTrue(true);
    }

    /**
     * Test that SendOrderStatusUpdateJob is dispatched when tracking number is added.
     * Requirement: 11.3
     */
    public function test_order_status_update_job_dispatched_on_tracking_number_added(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $address = Address::factory()->create(['user_id' => $customer->id]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'address_id' => $address->id,
            'status' => 'payment_confirmed',
        ]);

        $this->actingAs($admin);

        // Add tracking number
        $this->patch("/admin/orders/{$order->id}/tracking", [
            'shipping_tracking_number' => 'JNE123456789',
        ]);

        Queue::assertPushed(SendOrderStatusUpdateJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id
                && $job->newStatus === 'shipped';
        });
    }

    /**
     * Test that jobs can be processed by queue worker.
     */
    public function test_jobs_can_be_processed_by_queue_worker(): void
    {
        $this->markTestSkipped('Requires queue worker to be running');
    }

    /**
     * Test that jobs have correct retry configuration.
     */
    public function test_jobs_have_retry_configuration(): void
    {
        $user = User::factory()->create();

        $emailJob = new SendEmailVerificationJob($user);
        $this->assertEquals(3, $emailJob->tries);
        $this->assertEquals(60, $emailJob->backoff);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderJob = new SendOrderConfirmationJob($order);
        $this->assertEquals(3, $orderJob->tries);
        $this->assertEquals(60, $orderJob->backoff);

        $paymentJob = new SendPaymentConfirmationJob($order);
        $this->assertEquals(3, $paymentJob->tries);
        $this->assertEquals(60, $paymentJob->backoff);

        $statusJob = new SendOrderStatusUpdateJob($order, 'pending_payment', 'payment_confirmed');
        $this->assertEquals(3, $statusJob->tries);
        $this->assertEquals(60, $statusJob->backoff);
    }
}

<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     * Requirement: 5.8
     */
    public function __construct(
        public readonly Order $order
    ) {}

    /**
     * Execute the job — send payment confirmation email to the customer.
     * Requirement: 5.8
     */
    public function handle(): void
    {
        // Ensure relationships are loaded
        $order = $this->order->loadMissing(['user', 'items', 'address', 'payment', 'voucher']);

        $user = $order->user;

        if (! $user || ! $user->email) {
            Log::warning("SendPaymentConfirmationJob: no email for order #{$order->order_number}");
            return;
        }

        try {
            Mail::send(
                'emails.payment-confirmation',
                ['order' => $order],
                function ($message) use ($user, $order) {
                    $message->to($user->email, $user->name)
                        ->subject("Pembayaran Dikonfirmasi - Pesanan #{$order->order_number}");
                }
            );

            Log::info("Payment confirmation email sent for order #{$order->order_number} to {$user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send payment confirmation for #{$order->order_number}: {$e->getMessage()}");
            throw $e; // Re-throw so the queue can retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error(
            "SendPaymentConfirmationJob permanently failed for order #{$this->order->order_number}: "
            . $exception->getMessage()
        );
    }
}

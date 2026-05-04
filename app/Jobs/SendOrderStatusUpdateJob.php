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

class SendOrderStatusUpdateJob implements ShouldQueue
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
     * Requirement: 11.3
     */
    public function __construct(
        public readonly Order $order,
        public readonly string $oldStatus,
        public readonly string $newStatus
    ) {}

    /**
     * Execute the job — send order status update email to the customer.
     * Requirement: 11.3
     */
    public function handle(): void
    {
        // Ensure relationships are loaded
        $order = $this->order->loadMissing(['user', 'items', 'address']);

        $user = $order->user;

        if (! $user || ! $user->email) {
            Log::warning("SendOrderStatusUpdateJob: no email for order #{$order->order_number}");
            return;
        }

        try {
            $statusLabels = [
                'pending_payment' => 'Menunggu Pembayaran',
                'payment_confirmed' => 'Pembayaran Dikonfirmasi',
                'processing' => 'Diproses',
                'shipped' => 'Sedang Dikirim',
                'delivered' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
            $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

            Mail::send(
                'emails.order-status-update',
                [
                    'order' => $order,
                    'oldStatus' => $oldStatusLabel,
                    'newStatus' => $newStatusLabel,
                ],
                function ($message) use ($user, $order) {
                    $message->to($user->email, $user->name)
                        ->subject("Update Status Pesanan #{$order->order_number}");
                }
            );

            Log::info("Order status update email sent for order #{$order->order_number} to {$user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send order status update for #{$order->order_number}: {$e->getMessage()}");
            throw $e; // Re-throw so the queue can retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error(
            "SendOrderStatusUpdateJob permanently failed for order #{$this->order->order_number}: "
            . $exception->getMessage()
        );
    }
}

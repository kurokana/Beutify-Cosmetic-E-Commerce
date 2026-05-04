<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationJob implements ShouldQueue
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
     * Requirement: 1.1
     */
    public function __construct(
        public readonly User $user
    ) {}

    /**
     * Execute the job — send email verification notification to the user.
     * Requirement: 1.1
     */
    public function handle(): void
    {
        if (! $this->user->email) {
            Log::warning("SendEmailVerificationJob: no email for user #{$this->user->id}");
            return;
        }

        // Skip if already verified
        if ($this->user->hasVerifiedEmail()) {
            Log::info("SendEmailVerificationJob: user #{$this->user->id} already verified, skipping");
            return;
        }

        try {
            // Send the verification notification
            $this->user->notify(new VerifyEmail());

            Log::info("Email verification sent to user #{$this->user->id} ({$this->user->email})");
        } catch (\Throwable $e) {
            Log::error("Failed to send email verification for user #{$this->user->id}: {$e->getMessage()}");
            throw $e; // Re-throw so the queue can retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error(
            "SendEmailVerificationJob permanently failed for user #{$this->user->id}: "
            . $exception->getMessage()
        );
    }
}

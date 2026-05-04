<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->logActivity('created', $user, null, $user->toArray());
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->logActivity('updated', $user, $user->getOriginal(), $user->getChanges());
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->logActivity('deleted', $user, $user->toArray(), null);
    }

    /**
     * Log the activity to admin_logs table.
     */
    private function logActivity(string $action, User $user, ?array $oldValues, ?array $newValues): void
    {
        // Only log if the user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return;
        }

        // Don't log password changes in plain text
        if (isset($oldValues['password'])) {
            $oldValues['password'] = '[REDACTED]';
        }
        if (isset($newValues['password'])) {
            $newValues['password'] = '[REDACTED]';
        }

        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}

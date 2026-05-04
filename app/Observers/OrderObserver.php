<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->logActivity('created', $order, null, $order->toArray());
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $this->logActivity('updated', $order, $order->getOriginal(), $order->getChanges());
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $this->logActivity('deleted', $order, $order->toArray(), null);
    }

    /**
     * Log the activity to admin_logs table.
     */
    private function logActivity(string $action, Order $order, ?array $oldValues, ?array $newValues): void
    {
        // Only log if the user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return;
        }

        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'model_type' => Order::class,
            'model_id' => $order->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}

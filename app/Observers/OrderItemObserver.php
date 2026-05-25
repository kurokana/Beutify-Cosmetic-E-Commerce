<?php

namespace App\Observers;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        $this->invalidateBestSellerCache();
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        $this->invalidateBestSellerCache();
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        $this->invalidateBestSellerCache();
    }

    private function invalidateBestSellerCache(): void
    {
        Cache::forget('best_sellers');
    }
}

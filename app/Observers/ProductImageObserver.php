<?php

namespace App\Observers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Cache;

class ProductImageObserver
{
    /**
     * Handle the ProductImage "created" event.
     */
    public function created(ProductImage $image): void
    {
        $this->invalidateProductCaches($image);
    }

    /**
     * Handle the ProductImage "updated" event.
     */
    public function updated(ProductImage $image): void
    {
        $this->invalidateProductCaches($image);
    }

    /**
     * Handle the ProductImage "deleted" event.
     */
    public function deleted(ProductImage $image): void
    {
        $this->invalidateProductCaches($image);
    }

    private function invalidateProductCaches(ProductImage $image): void
    {
        Cache::forget('latest_products');
        Cache::forget('best_sellers');

        // Ensure product-level changes propagate to cached lists.
        $image->product?->touch();
    }
}

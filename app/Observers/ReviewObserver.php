<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     * Invalidate caches when a new review is added.
     * Requirements: 14.7 - Cache invalidation on data changes
     */
    public function created(Review $review): void
    {
        $this->invalidateReviewCaches($review);
    }

    /**
     * Handle the Review "deleted" event.
     * Invalidate caches when a review is deleted.
     * Requirements: 14.7 - Cache invalidation on data changes
     */
    public function deleted(Review $review): void
    {
        $this->invalidateReviewCaches($review);
    }

    /**
     * Invalidate all review-related caches.
     * This ensures that product ratings and best sellers are recalculated.
     */
    private function invalidateReviewCaches(Review $review): void
    {
        // Invalidate product-specific rating cache
        Cache::forget("product_avg_rating_{$review->product_id}");

        // Invalidate best sellers cache (ratings affect product popularity)
        Cache::forget('best_sellers');
    }
}

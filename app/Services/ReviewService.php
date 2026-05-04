<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ReviewService
{
    /**
     * Check whether the user has at least one delivered order containing this product.
     * Requirements: 7.1, 7.3
     */
    public function canReview(User $user, Product $product): bool
    {
        return Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();
    }

    /**
     * Check whether the user has already reviewed this product for the given order.
     * Requirements: 7.4
     */
    public function hasReviewed(User $user, Product $product, int $orderId): bool
    {
        return Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('order_id', $orderId)
            ->exists();
    }

    /**
     * Return the first delivered order that contains this product for the user.
     * Returns null if no such order exists.
     * Requirements: 7.1
     */
    public function getEligibleOrder(User $user, Product $product): ?Order
    {
        return Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->latest()
            ->first();
    }

    /**
     * Save a new review and recalculate the product's average rating.
     * Requirements: 7.2, 7.5
     *
     * @param  array{rating: int, comment: string, order_id: int}  $data
     */
    public function store(User $user, Product $product, array $data): Review
    {
        $review = Review::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'order_id'   => $data['order_id'],
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
        ]);

        $this->recalculateAverageRating($product);

        return $review;
    }

    /**
     * Delete a review and recalculate the product's average rating.
     * Requirements: 7.6
     */
    public function deleteReview(Review $review): void
    {
        $product = $review->product;

        $review->delete();

        $this->recalculateAverageRating($product);
    }

    /**
     * Recalculate and persist the average rating for a product using AVG() SQL,
     * rounded to one decimal place. Invalidates the per-product cache.
     * Requirements: 7.5, 7.6
     */
    private function recalculateAverageRating(Product $product): void
    {
        // Use AVG() SQL, rounded to one decimal place [Req 7.5]
        $average = Review::where('product_id', $product->id)->avg('rating');
        $rounded = round((float) $average, 1);

        $product->update(['average_rating' => $rounded]);

        // Invalidate cached average rating so next read reflects the new value
        Cache::forget("product_avg_rating_{$product->id}");

        // Re-cache the freshly computed value for fast subsequent reads
        Cache::remember("product_avg_rating_{$product->id}", 3600, fn () => $rounded);
    }
}

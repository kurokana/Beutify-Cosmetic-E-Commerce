<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * Delete a review from the admin panel.
     * Requirements: 7.6
     *
     * @param  Review  $review
     * @return RedirectResponse
     */
    public function destroy(Review $review): RedirectResponse
    {
        // Store product name for the success message
        $productName = $review->product->name;

        // Delete the review and invalidate cache for product rating
        $this->reviewService->deleteReview($review);

        return redirect()
            ->back()
            ->with('success', "Ulasan untuk produk \"{$productName}\" berhasil dihapus.");
    }
}

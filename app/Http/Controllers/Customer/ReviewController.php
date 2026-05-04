<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    /**
     * Store a new review for the given product.
     * Requirements: 7.1, 7.2, 7.3, 7.4
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        // Requirement 7.3: user must have a delivered order containing this product
        if (! $this->reviewService->canReview($user, $product)) {
            return redirect()
                ->route('catalog.show', $product->slug)
                ->with('error', 'Anda hanya dapat mengulas produk yang sudah dibeli dan pesanannya berstatus Selesai.');
        }

        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'comment'  => ['nullable', 'string', 'max:1000'],
        ]);

        // Requirement 7.4: prevent duplicate review for the same product + order
        if ($this->reviewService->hasReviewed($user, $product, (int) $validated['order_id'])) {
            return redirect()
                ->route('catalog.show', $product->slug)
                ->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // Requirement 7.2: save the review and invalidate cache
        $this->reviewService->store($user, $product, $validated);

        return redirect()
            ->route('catalog.show', $product->slug)
            ->with('success', 'Ulasan Anda berhasil disimpan. Terima kasih!');
    }
}

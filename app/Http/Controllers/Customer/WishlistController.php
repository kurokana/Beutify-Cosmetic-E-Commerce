<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Display the wishlist page with all saved products.
     * Requirements: 8.3
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['product.images', 'product.brand'])
            ->latest('created_at')
            ->get();

        return view('customer.wishlist.index', compact('wishlists'));
    }

    /**
     * Toggle a product in the wishlist (add if not present, remove if present).
     * Requirements: 8.1, 8.2, 8.5
     */
    public function toggle(Product $product): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            // Requirement 8.2: remove from wishlist if already there
            $existing->delete();
            return back()->with('success', 'Produk dihapus dari wishlist.');
        }

        // Requirement 8.1: add to wishlist
        Wishlist::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Produk ditambahkan ke wishlist.');
    }

    /**
     * Move a product from wishlist to cart.
     * Requirements: 8.4
     */
    public function moveToCart(Product $product): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            // Add to cart using CartService
            $this->cartService->addItem($user, $product->id, 1, null);

            // Remove from wishlist
            Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->delete();

            return redirect()->route('cart.index')
                ->with('success', 'Produk berhasil dipindahkan ke keranjang.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}

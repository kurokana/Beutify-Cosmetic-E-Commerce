<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Display the cart page.
     * Requirement: 3.4, 3.7, 3.8
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product.images', 'variant'])
            ->get()
            ->map(function (CartItem $item) {
                $item->unit_price = $this->cartService->getUnitPrice($item);
                $item->subtotal   = $item->unit_price * $item->quantity;
                return $item;
            });

        $total = $cartItems->sum('subtotal');

        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a product to the cart.
     * Requirements: 3.1, 3.2, 3.3
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'         => ['required', 'integer', 'exists:products,id'],
            'quantity'           => ['required', 'integer', 'min:1'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $this->cartService->addItem(
                $user,
                (int) $validated['product_id'],
                (int) $validated['quantity'],
                isset($validated['product_variant_id']) ? (int) $validated['product_variant_id'] : null
            );

            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Update the quantity of a cart item via AJAX.
     * Requirements: 3.3, 3.5
     */
    public function update(Request $request, CartItem $item): JsonResponse
    {
        // Authorization: ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $updatedItem = $this->cartService->updateQuantity($item, (int) $validated['quantity']);

            $unitPrice = $this->cartService->getUnitPrice($updatedItem);
            $subtotal  = $unitPrice * $updatedItem->quantity;

            /** @var \App\Models\User $user */
            $user  = Auth::user();
            $total = $this->cartService->getCartTotal($user);

            return response()->json([
                'success'  => true,
                'subtotal' => $subtotal,
                'total'    => $total,
                'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'total_formatted'    => 'Rp ' . number_format($total, 0, ',', '.'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }
    }

    /**
     * Remove a cart item.
     * Requirement: 3.6
     */
    public function destroy(CartItem $item): JsonResponse|RedirectResponse
    {
        // Authorization: ensure the item belongs to the authenticated user
        if ($item->user_id !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        $this->cartService->removeItem($item);

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $total = $this->cartService->getCartTotal($user);

        if (request()->expectsJson()) {
            return response()->json([
                'success'         => true,
                'total'           => $total,
                'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}

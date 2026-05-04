<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Add a product (with optional variant) to the user's cart.
     * If the same product+variant already exists, increment quantity instead.
     * Requirements: 3.1, 3.2, 3.3
     *
     * @throws ValidationException when requested quantity exceeds available stock
     */
    public function addItem(User $user, int $productId, int $quantity, ?int $variantId = null): CartItem
    {
        $product = Product::active()->findOrFail($productId);

        // Determine available stock (variant stock takes precedence when variant is selected)
        $variant = null;
        if ($variantId !== null) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->firstOrFail();
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->stock;
        }

        // Find existing cart item for this product+variant combination
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        $newQuantity = ($cartItem ? $cartItem->quantity : 0) + $quantity;

        // Validate stock — Requirement 3.3
        if ($newQuantity > $availableStock) {
            if ($availableStock <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok produk ini sudah habis.',
                ]);
            }
            throw ValidationException::withMessages([
                'quantity' => "Jumlah melebihi stok yang tersedia. Maksimum: {$availableStock}.",
            ]);
        }

        if ($cartItem) {
            // Requirement 3.2: increment quantity, not a new entry
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Requirement 3.1: create new cart item
            $cartItem = CartItem::create([
                'user_id'            => $user->id,
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'quantity'           => $quantity,
            ]);
        }

        return $cartItem->fresh(['product', 'variant']);
    }

    /**
     * Update the quantity of an existing cart item.
     * Requirements: 3.3, 3.5
     *
     * @throws ValidationException when requested quantity exceeds available stock
     */
    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah harus lebih dari 0.',
            ]);
        }

        // Determine available stock
        if ($item->product_variant_id !== null) {
            $availableStock = $item->variant?->stock ?? 0;
        } else {
            $availableStock = $item->product?->stock ?? 0;
        }

        // Requirement 3.3: validate stock
        if ($quantity > $availableStock) {
            throw ValidationException::withMessages([
                'quantity' => "Jumlah melebihi stok yang tersedia. Maksimum: {$availableStock}.",
            ]);
        }

        $item->update(['quantity' => $quantity]);

        return $item->fresh(['product', 'variant']);
    }

    /**
     * Remove a cart item.
     * Requirement: 3.6
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Calculate the total price of all items in the user's cart.
     * Requirements: 3.7
     */
    public function getCartTotal(User $user): float
    {
        $items = CartItem::where('user_id', $user->id)
            ->with(['product', 'variant'])
            ->get();

        return $items->sum(function (CartItem $item) {
            $unitPrice = $this->getUnitPrice($item);
            return $unitPrice * $item->quantity;
        });
    }

    /**
     * Get the total number of items (sum of quantities) in the user's cart.
     */
    public function getCartCount(User $user): int
    {
        return (int) CartItem::where('user_id', $user->id)->sum('quantity');
    }

    /**
     * Get all cart items for a user.
     * Requirements: 3.4, 3.8
     */
    public function getCartItems(User $user)
    {
        return CartItem::where('user_id', $user->id)
            ->with(['product', 'variant'])
            ->get();
    }

    /**
     * Calculate the unit price for a cart item (base price + variant additional price).
     */
    public function getUnitPrice(CartItem $item): float
    {
        $basePrice = (float) ($item->product?->price ?? 0);
        $additionalPrice = (float) ($item->variant?->additional_price ?? 0);

        return $basePrice + $additionalPrice;
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\CartService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for handling shipping cost calculation.
 *
 * Requirements: 4.3, 4.4, 6.1, 6.2
 */
class ShippingController extends Controller
{
    private ShippingService $shippingService;
    private CartService $cartService;

    public function __construct(ShippingService $shippingService, CartService $cartService)
    {
        $this->shippingService = $shippingService;
        $this->cartService     = $cartService;
    }

    /**
     * Calculate shipping cost via AJAX.
     *
     * Requirements: 4.3, 6.1
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function calculateCost(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
        ]);

        try {
            // Get the address
            $address = Address::findOrFail($request->address_id);

            // Verify the address belongs to the authenticated user
            if ($address->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak valid.',
                ], 403);
            }

            // Calculate total weight from cart items
            $cartItems   = $this->cartService->getCartItems(auth()->user());
            $totalWeight = $this->calculateTotalWeight($cartItems);

            if ($totalWeight <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang belanja kosong.',
                ], 400);
            }

            // Get shipping options
            $shippingOptions = $this->shippingService->calculateShippingCost($address, $totalWeight);

            return response()->json([
                'success' => true,
                'data'    => $shippingOptions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate total weight from cart items.
     *
     * @param \Illuminate\Support\Collection $cartItems
     *
     * @return int Total weight in grams
     */
    private function calculateTotalWeight($cartItems): int
    {
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $weight  = $product->weight ?? 0;
            $totalWeight += $weight * $item->quantity;
        }

        return $totalWeight;
    }
}

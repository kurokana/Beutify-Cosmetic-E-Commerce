<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create a new order from the user's cart within a database transaction.
     * Requirements: 4.1, 4.2, 4.7, 4.8
     *
     * Expected $data keys:
     *   - address_id      (int)
     *   - courier_name    (string)
     *   - courier_service (string)
     *   - shipping_cost   (numeric)
     *   - voucher_code    (string|null)
     *   - notes           (string|null)
     *
     * @throws ValidationException when cart is empty, stock is insufficient, or voucher is invalid
     */
    public function createOrder(User $user, array $data): Order
    {
        // Load cart items with product and variant
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang belanja Anda kosong.',
            ]);
        }

        // Resolve voucher (if provided)
        $voucher        = null;
        $discountAmount = 0;

        if (! empty($data['voucher_code'])) {
            $voucher = Voucher::where('code', $data['voucher_code'])->first();

            if (! $voucher || ! $voucher->isValid()) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Kode voucher tidak valid atau sudah kedaluwarsa.',
                ]);
            }
        }

        return DB::transaction(function () use ($user, $data, $cartItems, $voucher, $discountAmount) {
            // ── 1. Calculate subtotal ─────────────────────────────────────────
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $unitPrice = (float) $item->product->price
                    + (float) ($item->variant?->additional_price ?? 0);
                $subtotal += $unitPrice * $item->quantity;
            }

            // ── 2. Apply voucher discount ─────────────────────────────────────
            if ($voucher) {
                if ($subtotal < (float) $voucher->minimum_purchase) {
                    throw ValidationException::withMessages([
                        'voucher_code' => 'Minimum pembelian untuk voucher ini adalah Rp '
                            . number_format($voucher->minimum_purchase, 0, ',', '.') . '.',
                    ]);
                }

                if ($voucher->type === 'percentage') {
                    $discountAmount = $subtotal * ((float) $voucher->value / 100);
                } else {
                    $discountAmount = (float) $voucher->value;
                }

                $discountAmount = min($discountAmount, $subtotal);
            }

            $shippingCost = (float) ($data['shipping_cost'] ?? 0);
            $totalAmount  = $subtotal - $discountAmount + $shippingCost;

            // ── 3. Create order record ────────────────────────────────────────
            $order = Order::create([
                'order_number'    => $this->generateOrderNumber(),
                'user_id'         => $user->id,
                'address_id'      => $data['address_id'],
                'courier_name'    => $data['courier_name'],
                'courier_service' => $data['courier_service'],
                'shipping_cost'   => $shippingCost,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'voucher_id'      => $voucher?->id,
                'status'          => 'pending_payment',
                'notes'           => $data['notes'] ?? null,
            ]);

            // ── 4. Create order items + reduce stock ──────────────────────────
            foreach ($cartItems as $item) {
                $unitPrice = (float) $item->product->price
                    + (float) ($item->variant?->additional_price ?? 0);
                $itemSubtotal = $unitPrice * $item->quantity;

                // Validate stock before reducing
                if ($item->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if (! $variant || $variant->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'stock' => "Stok varian produk \"{$item->product->name}\" tidak mencukupi.",
                        ]);
                    }
                    $variant->decrement('stock', $item->quantity);
                } else {
                    $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                    if (! $product || $product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'stock' => "Stok produk \"{$item->product->name}\" tidak mencukupi.",
                        ]);
                    }
                    $product->decrement('stock', $item->quantity);
                }

                $order->items()->create([
                    'product_id'         => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name'       => $item->product->name,
                    'variant_name'       => $item->variant
                        ? ($item->variant->name . ': ' . $item->variant->value)
                        : null,
                    'price'    => $unitPrice,
                    'quantity' => $item->quantity,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            // ── 5. Create payment record (pending) ────────────────────────────
            Payment::create([
                'order_id'          => $order->id,
                'midtrans_order_id' => $order->order_number,
                'amount'            => $totalAmount,
                'status'            => 'pending',
                'expired_at'        => now()->addHours(24),
            ]);

            // ── 6. Increment voucher usage ────────────────────────────────────
            if ($voucher) {
                $this->voucherService->incrementUsage($voucher);
            }

            // ── 7. Clear user's cart ──────────────────────────────────────────
            CartItem::where('user_id', $user->id)->delete();

            // ── 8. Dispatch confirmation email job (Requirement 4.8) ──────────
            SendOrderConfirmationJob::dispatch($order->load(['items', 'address', 'payment']));

            return $order;
        });
    }

    /**
     * Generate a unique order number in the format ORD-YYYYMMDD-XXXXX.
     * The 5-digit suffix is random and retried on collision.
     */
    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        do {
            $suffix      = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $orderNumber = "ORD-{$date}-{$suffix}";
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}

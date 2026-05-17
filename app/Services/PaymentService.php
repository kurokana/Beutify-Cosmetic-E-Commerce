<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentService
{
    public function __construct()
    {
        // Configure Midtrans using config/midtrans.php
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$clientKey    = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Create a Midtrans Snap transaction for the given order, persist the
     * snap_token to the payments table, and return the token.
     *
     * Requirements: 5.1, 5.2
     *
     * @throws \Exception when Midtrans API call fails
     */
    public function createSnapTransaction(Order $order, bool $forceRefresh = false): string
    {
        // Eager-load relations needed for the transaction payload
        $order->loadMissing(['items', 'user', 'address', 'payment']);

        /** @var Payment $payment */
        $payment = $order->payment;

        if (! $forceRefresh && $payment && $this->shouldReuseSnapToken($payment)) {
            return $payment->snap_token;
        }

        $midtransOrderId = $payment?->midtrans_order_id ?? $order->order_number;

        if ($payment && ($forceRefresh || $this->shouldRefreshPayment($payment))) {
            $midtransOrderId = $this->buildMidtransOrderId($order);
            $payment->update([
                'midtrans_order_id' => $midtransOrderId,
                'midtrans_transaction_id' => null,
                'payment_method' => null,
                'payment_type' => null,
                'status' => 'pending',
                'snap_token' => null,
                'paid_at' => null,
                'expired_at' => now()->addHours(24),
            ]);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) round((float) $order->total_amount),
            ],
            'customer_details' => $this->buildCustomerDetails($order),
            'item_details'     => $this->buildItemDetails($order),
        ];

        // Call Midtrans Snap API
        $snapToken = Snap::getSnapToken($params);

        // Persist the snap_token
        if ($payment) {
            $payment->update([
                'midtrans_order_id' => $midtransOrderId,
                'snap_token' => $snapToken,
            ]);
        } else {
            Payment::create([
                'order_id'          => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'amount'            => $order->total_amount,
                'status'            => 'pending',
                'snap_token'        => $snapToken,
                'expired_at'        => now()->addHours(24),
            ]);
        }

        return $snapToken;
    }

    /**
     * Sync payment status from Midtrans and update local records.
     * Returns the mapped payment status when available.
     */
    public function syncPaymentStatus(Order $order): ?string
    {
        $order->loadMissing(['payment', 'items.product', 'items.variant']);

        $payment = $order->payment;
        if (! $payment || ! $payment->midtrans_order_id) {
            return null;
        }

        try {
            $raw = Transaction::status($payment->midtrans_order_id);
        } catch (\Throwable $e) {
            return null;
        }

        $payload = is_array($raw) ? $raw : (array) $raw;
        $transactionStatus = data_get($payload, 'transaction_status');
        $fraudStatus = data_get($payload, 'fraud_status');

        if (! $transactionStatus) {
            return null;
        }

        $newPaymentStatus = $this->mapTransactionStatus((string) $transactionStatus, $fraudStatus ? (string) $fraudStatus : null);

        if ($payment->status === 'success' && $newPaymentStatus !== 'success') {
            return $payment->status;
        }

        $transactionId = data_get($payload, 'transaction_id');
        $paymentType = data_get($payload, 'payment_type');

        DB::transaction(function () use ($order, $payment, $newPaymentStatus, $transactionId, $paymentType) {
            $payment->update([
                'status' => $newPaymentStatus,
                'midtrans_transaction_id' => $transactionId,
                'payment_type' => $paymentType,
                'paid_at' => $newPaymentStatus === 'success' ? now() : null,
            ]);

            if ($newPaymentStatus === 'success') {
                if ($order->status === 'pending_payment') {
                    $order->update(['status' => 'payment_confirmed']);
                }
            } elseif (in_array($newPaymentStatus, ['expired', 'failed', 'cancelled'], true)) {
                if ($order->status !== 'cancelled') {
                    $order->update(['status' => 'cancelled']);
                    $this->restoreStock($order);
                }
            }
        });

        return $newPaymentStatus;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Build the customer_details array from the order's user and address.
     */
    private function buildCustomerDetails(Order $order): array
    {
        $user    = $order->user;
        $address = $order->address;

        $details = [
            'first_name' => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone ?? ($address?->phone ?? ''),
        ];

        if ($address) {
            $details['billing_address'] = [
                'first_name'   => $address->recipient_name,
                'phone'        => $address->phone,
                'address'      => $address->full_address,
                'city'         => $address->city,
                'postal_code'  => $address->postal_code,
                'country_code' => 'IDN',
            ];
            $details['shipping_address'] = $details['billing_address'];
        }

        return $details;
    }

    /**
     * Build the item_details array from order items plus shipping cost.
     */
    private function buildItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $name = $item->product_name;
            if ($item->variant_name) {
                $name .= ' (' . $item->variant_name . ')';
            }

            $items[] = [
                'id'       => (string) $item->id,
                'price'    => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
                'name'     => mb_substr($name, 0, 50), // Midtrans max 50 chars
            ];
        }

        // Add shipping cost as a line item if applicable
        if ((float) $order->shipping_cost > 0) {
            $items[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) round((float) $order->shipping_cost),
                'quantity' => 1,
                'name'     => 'Ongkos Kirim (' . strtoupper($order->courier_name) . ' ' . $order->courier_service . ')',
            ];
        }

        // Add discount as a negative line item if applicable
        if ((float) $order->discount_amount > 0) {
            $items[] = [
                'id'       => 'DISCOUNT',
                'price'    => -(int) round((float) $order->discount_amount),
                'quantity' => 1,
                'name'     => 'Diskon Voucher',
            ];
        }

        return $items;
    }

    private function shouldReuseSnapToken(Payment $payment): bool
    {
        if (! $payment->snap_token || $payment->status !== 'pending') {
            return false;
        }

        return ! $payment->expired_at || $payment->expired_at->isFuture();
    }

    private function shouldRefreshPayment(Payment $payment): bool
    {
        if ($payment->status !== 'pending') {
            return true;
        }

        return $payment->expired_at && $payment->expired_at->isPast();
    }

    private function buildMidtransOrderId(Order $order): string
    {
        return $order->order_number . '-' . now()->format('YmdHis');
    }

    private function mapTransactionStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($fraudStatus === 'deny') {
            return 'failed';
        }

        return match ($transactionStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'cancel' => 'cancelled',
            'expire' => 'expired',
            'failure' => 'failed',
            default => 'pending',
        };
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variant_id) {
                $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                }
            } else {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
    }
}

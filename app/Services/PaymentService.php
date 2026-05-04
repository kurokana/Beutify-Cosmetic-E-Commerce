<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

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
    public function createSnapTransaction(Order $order): string
    {
        // Eager-load relations needed for the transaction payload
        $order->loadMissing(['items', 'user', 'address', 'payment']);

        /** @var Payment $payment */
        $payment = $order->payment;

        // If a valid snap_token already exists, reuse it
        if ($payment && $payment->snap_token) {
            return $payment->snap_token;
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) round((float) $order->total_amount),
            ],
            'customer_details' => $this->buildCustomerDetails($order),
            'item_details'     => $this->buildItemDetails($order),
        ];

        // Call Midtrans Snap API
        $snapToken = Snap::getSnapToken($params);

        // Persist the snap_token
        if ($payment) {
            $payment->update(['snap_token' => $snapToken]);
        } else {
            Payment::create([
                'order_id'          => $order->id,
                'midtrans_order_id' => $order->order_number,
                'amount'            => $order->total_amount,
                'status'            => 'pending',
                'snap_token'        => $snapToken,
                'expired_at'        => now()->addHours(24),
            ]);
        }

        return $snapToken;
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
}

<?php

namespace App\Http\Controllers;

use App\Jobs\SendPaymentConfirmationJob;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle Midtrans payment notification webhook.
     *
     * Requirements: 5.6, 5.7, 5.8
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        // Extract notification data
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        $transactionId = $request->input('transaction_id');
        $paymentType = $request->input('payment_type');

        Log::info("Midtrans webhook received for order: {$orderId}", [
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        // Verify signature (Requirement 5.6)
        if (!$this->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning("Midtrans webhook signature verification failed for order: {$orderId}");
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        // Find payment by midtrans_order_id
        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (!$payment) {
            Log::warning("Payment not found for Midtrans order_id: {$orderId}");
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        // Load the order
        $order = $payment->order;

        if (!$order) {
            Log::warning("Order not found for payment ID: {$payment->id}");
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // Determine the new status based on transaction_status (Requirements 5.6, 5.7)
        $newPaymentStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

        // Update payment and order status in a transaction
        DB::transaction(function () use ($payment, $order, $newPaymentStatus, $transactionId, $paymentType) {
            // Update payment record
            $payment->update([
                'status' => $newPaymentStatus,
                'midtrans_transaction_id' => $transactionId,
                'payment_type' => $paymentType,
                'paid_at' => $newPaymentStatus === 'success' ? now() : null,
            ]);

            // Update order status based on payment status
            if ($newPaymentStatus === 'success') {
                // Payment confirmed (Requirement 5.6)
                $order->update(['status' => 'payment_confirmed']);

                // Dispatch job to send payment confirmation email (Requirement 5.8)
                SendPaymentConfirmationJob::dispatch($order);

                Log::info("Order #{$order->order_number} payment confirmed");
            } elseif (in_array($newPaymentStatus, ['expired', 'failed', 'cancelled'])) {
                // Payment failed/expired/cancelled → cancel order and restore stock (Requirement 5.7)
                $order->update(['status' => 'cancelled']);

                // Restore stock for all order items
                $this->restoreStock($order);

                Log::info("Order #{$order->order_number} cancelled, stock restored");
            }
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify Midtrans webhook signature.
     * Signature = SHA512(order_id + status_code + gross_amount + server_key)
     *
     * Requirement: 5.6
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    private function verifySignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Map Midtrans transaction_status to our payment status.
     *
     * @param string $transactionStatus
     * @param string|null $fraudStatus
     * @return string
     */
    private function mapTransactionStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        // Handle fraud status first
        if ($fraudStatus === 'deny') {
            return 'failed';
        }

        // Map transaction status
        return match ($transactionStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'cancel' => 'cancelled',
            'expire' => 'expired',
            'failure' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Restore stock for cancelled orders.
     * Restores stock for both products and variants.
     *
     * Requirement: 5.7
     *
     * @param Order $order
     * @return void
     */
    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items.product', 'items.variant');

        foreach ($order->items as $item) {
            if ($item->product_variant_id && $item->variant) {
                // Restore variant stock
                $item->variant->increment('stock', $item->quantity);
                Log::info("Restored variant stock: variant_id={$item->product_variant_id}, quantity={$item->quantity}");
            } elseif ($item->product) {
                // Restore product stock
                $item->product->increment('stock', $item->quantity);
                Log::info("Restored product stock: product_id={$item->product_id}, quantity={$item->quantity}");
            }
        }
    }
}

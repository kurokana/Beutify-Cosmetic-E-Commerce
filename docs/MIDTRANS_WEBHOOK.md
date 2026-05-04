# Midtrans Webhook Integration

## Overview

This document describes the Midtrans webhook integration for handling payment notifications in the kosmetik-ecommerce application.

## Endpoint

**URL:** `POST /api/webhook/midtrans`  
**Name:** `webhook.midtrans`  
**CSRF Protection:** Excluded (verified via signature instead)

## Webhook Flow

```
Midtrans → POST /api/webhook/midtrans → Verify Signature → Update Payment & Order → Dispatch Email Job
```

## Signature Verification

The webhook verifies authenticity using SHA512 signature:

```
signature = SHA512(order_id + status_code + gross_amount + server_key)
```

If signature verification fails, the webhook returns `403 Forbidden`.

## Payment Status Mapping

| Midtrans Status | Our Status | Action |
|----------------|------------|--------|
| `settlement`, `capture` | `success` | Update order to `payment_confirmed`, dispatch email |
| `pending` | `pending` | No action |
| `expire` | `expired` | Cancel order, restore stock |
| `deny`, `cancel` | `cancelled` | Cancel order, restore stock |
| `failure` | `failed` | Cancel order, restore stock |

## Stock Restoration

When an order is cancelled (expired/failed/denied), the webhook automatically restores stock:

- For products without variants: increments `products.stock`
- For products with variants: increments `product_variants.stock`

## Email Notifications

When payment is successful, the webhook dispatches `SendPaymentConfirmationJob` which:

- Sends email to customer with payment confirmation
- Includes order details, shipping info, and next steps
- Retries up to 3 times with 60-second backoff on failure

## Testing

Run the webhook tests:

```bash
php artisan test --filter=MidtransWebhookTest
```

## Configuration

Ensure these environment variables are set:

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

## Security Notes

1. **Signature Verification:** Always verify the signature before processing
2. **CSRF Exclusion:** The webhook is excluded from CSRF protection but protected by signature
3. **Idempotency:** The webhook can be called multiple times safely (updates are idempotent)
4. **Logging:** All webhook calls are logged for audit purposes

## Requirements Fulfilled

- **5.6:** Webhook signature verification and status update within 30 seconds
- **5.7:** Stock restoration for cancelled/expired orders
- **5.8:** Email notification dispatch on successful payment

## Example Webhook Payload

```json
{
  "order_id": "ORD-20240101-00001",
  "status_code": "200",
  "gross_amount": "100000.00",
  "signature_key": "abc123...",
  "transaction_status": "settlement",
  "fraud_status": "accept",
  "transaction_id": "TXN-123456",
  "payment_type": "bank_transfer"
}
```

## Troubleshooting

### Webhook not receiving notifications

1. Check Midtrans dashboard webhook URL configuration
2. Ensure the URL is publicly accessible (use ngrok for local testing)
3. Check server logs for incoming requests

### Signature verification fails

1. Verify `MIDTRANS_SERVER_KEY` matches the dashboard
2. Check that `gross_amount` format matches (decimal with 2 places)
3. Ensure no extra whitespace in environment variables

### Stock not restored

1. Check that order items have valid `product_id` or `product_variant_id`
2. Verify relationships are properly loaded
3. Check application logs for errors

## Local Testing

For local testing, use ngrok to expose your local server:

```bash
ngrok http 8000
```

Then configure the webhook URL in Midtrans dashboard:

```
https://your-ngrok-url.ngrok.io/api/webhook/midtrans
```

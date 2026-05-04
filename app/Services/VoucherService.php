<?php

namespace App\Services;

use App\Models\Voucher;

class VoucherService
{
    /**
     * Validate a voucher code against the given subtotal.
     *
     * Returns an array with:
     *   - valid           (bool)
     *   - message         (string)
     *   - discount_amount (float)
     *   - voucher         (Voucher|null)
     *
     * Requirements: 4.5, 4.6, 10.5, 10.6
     */
    public function validate(string $code, float $subtotal): array
    {
        $voucher = Voucher::where('code', strtoupper(trim($code)))->first();

        // Requirement 4.6 — voucher not found
        if (! $voucher) {
            return [
                'valid'           => false,
                'message'         => 'Kode voucher tidak ditemukan.',
                'discount_amount' => 0.0,
                'voucher'         => null,
            ];
        }

        // Requirement 10.5 — admin deactivated the voucher
        if (! $voucher->is_active) {
            return [
                'valid'           => false,
                'message'         => 'Voucher ini sudah tidak aktif.',
                'discount_amount' => 0.0,
                'voucher'         => null,
            ];
        }

        // Requirement 4.6 — voucher expired
        if ($voucher->expires_at->isPast()) {
            return [
                'valid'           => false,
                'message'         => 'Voucher ini sudah kedaluwarsa.',
                'discount_amount' => 0.0,
                'voucher'         => null,
            ];
        }

        // Requirement 10.6 — usage limit reached
        if ($voucher->max_usage !== null && $voucher->used_count >= $voucher->max_usage) {
            return [
                'valid'           => false,
                'message'         => 'Voucher ini sudah mencapai batas penggunaan.',
                'discount_amount' => 0.0,
                'voucher'         => null,
            ];
        }

        // Requirement 4.5 — minimum purchase check
        if ($subtotal < (float) $voucher->minimum_purchase) {
            $minFormatted = 'Rp ' . number_format($voucher->minimum_purchase, 0, ',', '.');

            return [
                'valid'           => false,
                'message'         => "Minimum pembelian untuk voucher ini adalah {$minFormatted}.",
                'discount_amount' => 0.0,
                'voucher'         => null,
            ];
        }

        // All checks passed — calculate discount
        $discountAmount = $this->calculateDiscount($voucher, $subtotal);

        return [
            'valid'           => true,
            'message'         => 'Voucher berhasil diterapkan!',
            'discount_amount' => $discountAmount,
            'voucher'         => $voucher,
        ];
    }

    /**
     * Calculate the discount amount for a valid voucher.
     *
     * For percentage vouchers the discount is capped at the subtotal so the
     * total never goes negative.
     *
     * Requirements: 4.5
     */
    public function calculateDiscount(Voucher $voucher, float $subtotal): float
    {
        if ($voucher->type === 'percentage') {
            $discount = $subtotal * ((float) $voucher->value / 100);
        } else {
            // fixed
            $discount = (float) $voucher->value;
        }

        // Discount cannot exceed the subtotal
        return min($discount, $subtotal);
    }

    /**
     * Increment the usage count of a voucher and auto-deactivate if max usage reached.
     *
     * Requirements: 10.6
     */
    public function incrementUsage(Voucher $voucher): void
    {
        $voucher->increment('used_count');

        // Auto-deactivate if max usage reached (Requirement 10.6)
        if ($voucher->max_usage !== null && $voucher->used_count >= $voucher->max_usage) {
            $voucher->update(['is_active' => false]);
        }
    }
}

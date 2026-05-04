<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    /**
     * Validate a voucher code via AJAX and return the discount amount.
     *
     * POST /voucher/validate
     * Requirements: 4.5, 4.6, 10.5, 10.6
     *
     * Request body (JSON):
     *   - voucher_code (string, required)
     *   - subtotal     (numeric, required, min:0)
     *
     * Response (JSON):
     *   - success         (bool)
     *   - message         (string)
     *   - discount_amount (float)   — only present when success is true
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'voucher_code' => ['required', 'string', 'max:50'],
            'subtotal'     => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->voucherService->validate(
            $request->input('voucher_code'),
            (float) $request->input('subtotal'),
        );

        if ($result['valid']) {
            return response()->json([
                'success'         => true,
                'message'         => $result['message'],
                'discount_amount' => $result['discount_amount'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }
}

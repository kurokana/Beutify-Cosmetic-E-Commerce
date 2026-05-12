<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    /**
     * Store a newly created address for the authenticated user.
     * - Prevent duplicates (same recipient + phone + full_address)
     * - Limit to 5 addresses per user
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'full_address' => ['required', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        // Limit addresses per user
        $count = Address::where('user_id', $user->id)->count();
        if ($count >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah menyimpan maksimal 5 alamat.',
            ], 422);
        }

        // Duplicate detection: same recipient, phone, and full_address
        $exists = Address::where('user_id', $user->id)
            ->where('recipient_name', $validated['recipient_name'])
            ->where('phone', $validated['phone'])
            ->where('full_address', $validated['full_address'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat sudah terdaftar.',
            ], 409);
        }

        // If is_default is true, unset other defaults
        if (! empty($validated['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address = Address::create(array_merge($validated, [
            'user_id' => $user->id,
            'is_default' => ! empty($validated['is_default']),
        ]));

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Alamat berhasil ditambahkan.',
        ]);
    }
}

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
            'province_id' => ['nullable', 'integer'],
            'province' => ['required', 'string', 'max:100'],
            'city_id' => ['nullable', 'integer'],
            'city' => ['required', 'string', 'max:100'],
            'district_id' => ['nullable', 'integer'],
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

    /**
     * Update an existing address for the authenticated user.
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($address->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'province_id' => ['nullable', 'integer'],
            'province' => ['required', 'string', 'max:100'],
            'city_id' => ['nullable', 'integer'],
            'city' => ['required', 'string', 'max:100'],
            'district_id' => ['nullable', 'integer'],
            'district' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'full_address' => ['required', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        // If is_default is true, unset other defaults
        if (! empty($validated['is_default'])) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update(array_merge($validated, [
            'is_default' => ! empty($validated['is_default']),
        ]));

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Alamat berhasil diperbarui.',
        ]);
    }

    /**
     * Delete an address for the authenticated user.
     */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($address->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Prevent deleting if it's the only address and is default
        $count = Address::where('user_id', $user->id)->count();
        if ($count === 1 && $address->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus alamat utama saat alamat lain tidak ada.',
            ], 422);
        }

        // If deleting default address, set another as default
        if ($address->is_default) {
            $nextDefault = Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus.',
        ]);
    }
}

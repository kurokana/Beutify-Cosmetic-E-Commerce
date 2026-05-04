<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Display a listing of all vouchers.
     * Requirements: 10.4
     */
    public function index(): View
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new voucher.
     * Requirements: 10.3
     */
    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    /**
     * Store a newly created voucher in storage.
     * Requirements: 10.3, 10.4
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_usage' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['required', 'date', 'after:today'],
            'is_active' => ['boolean'],
        ], [
            'code.unique' => 'Kode voucher sudah digunakan',
            'expires_at.after' => 'Tanggal kedaluwarsa harus setelah hari ini',
        ]);

        try {
            // Validate value based on type
            if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
                return back()
                    ->withInput()
                    ->withErrors(['value' => 'Nilai persentase tidak boleh lebih dari 100']);
            }

            Voucher::create([
                'code' => strtoupper(trim($validated['code'])),
                'type' => $validated['type'],
                'value' => $validated['value'],
                'minimum_purchase' => $validated['minimum_purchase'] ?? 0,
                'max_usage' => $validated['max_usage'],
                'used_count' => 0,
                'is_active' => $request->boolean('is_active', true),
                'expires_at' => $validated['expires_at'],
            ]);

            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Voucher berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan voucher: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified voucher.
     * Requirements: 10.3
     */
    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Update the specified voucher in storage.
     * Requirements: 10.3, 10.4
     */
    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($voucher->id)],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_usage' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['required', 'date'],
            'is_active' => ['boolean'],
        ], [
            'code.unique' => 'Kode voucher sudah digunakan',
        ]);

        try {
            // Validate value based on type
            if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
                return back()
                    ->withInput()
                    ->withErrors(['value' => 'Nilai persentase tidak boleh lebih dari 100']);
            }

            $voucher->update([
                'code' => strtoupper(trim($validated['code'])),
                'type' => $validated['type'],
                'value' => $validated['value'],
                'minimum_purchase' => $validated['minimum_purchase'] ?? 0,
                'max_usage' => $validated['max_usage'],
                'is_active' => $request->boolean('is_active', true),
                'expires_at' => $validated['expires_at'],
            ]);

            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Voucher berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui voucher: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified voucher from storage.
     * Requirements: 10.4
     */
    public function destroy(Voucher $voucher): RedirectResponse
    {
        // Check if voucher has been used in any orders
        $ordersCount = $voucher->orders()->count();

        if ($ordersCount > 0) {
            return back()->with('error', "Tidak dapat menghapus voucher karena sudah digunakan dalam {$ordersCount} pesanan.");
        }

        try {
            $voucher->delete();

            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', 'Voucher berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus voucher: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of the specified voucher.
     * Requirements: 10.5
     */
    public function toggleActive(Voucher $voucher): RedirectResponse
    {
        $voucher->update([
            'is_active' => !$voucher->is_active,
        ]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Voucher berhasil {$status}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display a listing of all brands.
     * Requirements: 9.5
     */
    public function index(): View
    {
        $brands = Brand::withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     * Requirements: 9.6
     */
    public function create(): View
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in storage.
     * Requirements: 9.6, 9.7
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        try {
            $logoPath = null;

            // Upload logo if provided
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('brands', 'public');
            }

            Brand::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'logo_path' => $logoPath,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Merek berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Clean up uploaded file if brand creation fails
            if (isset($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan merek: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified brand.
     * Requirements: 9.6
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     * Requirements: 9.6, 9.7
     */
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brand->id)],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        try {
            $oldLogoPath = $brand->logo_path;
            $logoPath = $oldLogoPath;

            // Upload new logo if provided
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('brands', 'public');
            }

            $brand->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'logo_path' => $logoPath,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Delete old logo if a new one was uploaded
            if ($request->hasFile('logo') && $oldLogoPath) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Merek berhasil diperbarui.');
        } catch (\Exception $e) {
            // Clean up uploaded file if update fails
            if ($request->hasFile('logo') && isset($logoPath) && $logoPath !== $oldLogoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui merek: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand from storage.
     * Requirements: 9.8
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        // Check if brand has any products (active or inactive)
        // Due to foreign key constraint with restrictOnDelete, we cannot delete if ANY products exist
        $productsCount = $brand->products()->count();
        $activeProductsCount = $brand->products()->where('is_active', true)->count();

        if ($productsCount > 0) {
            if ($activeProductsCount > 0) {
                return back()->with('error', "Tidak dapat menghapus merek karena masih memiliki {$activeProductsCount} produk aktif.");
            } else {
                return back()->with('error', "Tidak dapat menghapus merek karena masih memiliki {$productsCount} produk terdaftar. Hapus produk terlebih dahulu.");
            }
        }

        try {
            // Delete logo from storage if exists
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }

            $brand->delete();

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Merek berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus merek: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of the specified brand.
     * Requirements: 9.6
     */
    public function toggleActive(Brand $brand): RedirectResponse
    {
        $brand->update([
            'is_active' => !$brand->is_active,
        ]);

        $status = $brand->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Merek berhasil {$status}.");
    }

    /**
     * Delete the logo of the specified brand.
     * Requirements: 9.7
     */
    public function deleteLogo(Brand $brand): RedirectResponse
    {
        if (!$brand->logo_path) {
            return back()->with('error', 'Merek tidak memiliki logo.');
        }

        try {
            Storage::disk('public')->delete($brand->logo_path);

            $brand->update(['logo_path' => null]);

            return back()->with('success', 'Logo berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus logo: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     * Requirements: 10.1
     */
    public function index(): View
    {
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     * Requirements: 10.1
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     * Requirements: 10.1, 10.2
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'Nama kategori sudah digunakan',
        ]);

        try {
            Category::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     * Requirements: 10.1
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     * Requirements: 10.1, 10.2
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'Nama kategori sudah digunakan',
        ]);

        try {
            $category->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     * Requirements: 10.1
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has any products (active or inactive)
        $productsCount = $category->products()->count();
        $activeProductsCount = $category->products()->where('is_active', true)->count();

        if ($productsCount > 0) {
            if ($activeProductsCount > 0) {
                return back()->with('error', "Tidak dapat menghapus kategori karena masih memiliki {$activeProductsCount} produk aktif.");
            } else {
                return back()->with('error', "Tidak dapat menghapus kategori karena masih memiliki {$productsCount} produk terdaftar. Hapus produk terlebih dahulu.");
            }
        }

        try {
            $category->delete();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}

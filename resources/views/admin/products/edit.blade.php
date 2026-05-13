<x-admin-layout>
    <x-slot name="pageTitle">Edit Produk</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('admin.products.index') }}" class="hover:text-pink-600 transition-colors">Produk</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Edit Produk</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Produk: {{ $product->name }}</h2>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                {{-- Product Name --}}
                <div>
                    <x-input-label for="name" value="Nama Produk *" />
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('name', $product->name)"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="description" value="Deskripsi *" />
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm"
                        required
                    >{{ old('description', $product->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                {{-- Brand & Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="brand_id" value="Merek *" />
                        <select
                            id="brand_id"
                            name="brand_id"
                            class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">Pilih Merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="category_id" value="Kategori *" />
                        <select
                            id="category_id"
                            name="category_id"
                            class="mt-1 block w-full border-gray-300 focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                </div>

                {{-- Price, Stock, Weight --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="price" value="Harga (Rp) *" />
                        <x-text-input
                            id="price"
                            name="price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full"
                            :value="old('price', $product->price)"
                            required
                        />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="stock" value="Stok *" />
                        <x-text-input
                            id="stock"
                            name="stock"
                            type="number"
                            min="0"
                            class="mt-1 block w-full"
                            :value="old('stock', $product->stock)"
                            required
                        />
                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="weight" value="Berat (gram) *" />
                        <x-text-input
                            id="weight"
                            name="weight"
                            type="number"
                            min="1"
                            class="mt-1 block w-full"
                            :value="old('weight', $product->weight)"
                            required
                        />
                        <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                    </div>
                </div>

                {{-- SKU --}}
                <div>
                    <x-input-label for="sku" value="SKU *" />
                    <x-text-input
                        id="sku"
                        name="sku"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('sku', $product->sku)"
                        required
                    />
                    <p class="mt-1 text-sm text-gray-500">Kode unik untuk identifikasi produk</p>
                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                </div>

                {{-- Existing Images --}}
                @if ($product->images->count() > 0)
                    <div>
                        <x-input-label value="Gambar Saat Ini" />
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($product->images as $image)
                                <div class="relative group">
                                    <img
                                        src="{{ Storage::url($image->image_path) }}"
                                        alt="Product image"
                                        class="w-full h-32 object-cover rounded-lg"
                                    >
                                    @if ($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-pink-600 text-white text-xs px-2 py-1 rounded">
                                            Utama
                                        </span>
                                    @endif
                                    <button
                                        type="button"
                                        onclick="if (confirm('Hapus gambar ini?')) document.getElementById('delete-image-{{ $image->id }}').submit()"
                                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-red-600 text-white p-1.5 rounded-lg hover:bg-red-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add New Images --}}
                <div>
                    <x-input-label for="images" value="Tambah Gambar Baru (Opsional)" />
                    <input
                        id="images"
                        name="images[]"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        multiple
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                    />
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, WebP. Maksimal 2MB per file.</p>
                    <x-input-error :messages="$errors->get('images')" class="mt-2" />
                    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                </div>

                {{-- Active Status --}}
                <div class="flex items-center">
                    <input
                        id="is_active"
                        name="is_active"
                        type="checkbox"
                        value="1"
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                    />
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Aktifkan produk (tampilkan di katalog)
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-4">
                <a
                    href="{{ route('admin.products.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                >
                    Batal
                </a>
                <x-primary-button>
                    Perbarui Produk
                </x-primary-button>
            </div>
        </form>

        @foreach ($product->images as $image)
            <form
                id="delete-image-{{ $image->id }}"
                action="{{ route('admin.product-images.destroy', $image) }}"
                method="POST"
                class="hidden"
            >
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</x-admin-layout>

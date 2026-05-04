<x-admin-layout>
    <x-slot name="pageTitle">Tambah Produk</x-slot>

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('admin.products.index') }}" class="hover:text-pink-600 transition-colors">Produk</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Tambah Produk</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h2>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                {{-- Product Name --}}
                <div>
                    <x-input-label for="name" value="Nama Produk *" />
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('name')"
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
                    >{{ old('description') }}</textarea>
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
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            :value="old('price')"
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
                            :value="old('stock', 0)"
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
                            :value="old('weight')"
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
                        :value="old('sku')"
                        required
                    />
                    <p class="mt-1 text-sm text-gray-500">Kode unik untuk identifikasi produk</p>
                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                </div>

                {{-- Images --}}
                <div>
                    <x-input-label for="images" value="Gambar Produk *" />
                    <input
                        id="images"
                        name="images[]"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        multiple
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                        required
                    />
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, WebP. Maksimal 2MB per file. Gambar pertama akan menjadi gambar utama.</p>
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
                        {{ old('is_active', true) ? 'checked' : '' }}
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
                    Simpan Produk
                </x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>

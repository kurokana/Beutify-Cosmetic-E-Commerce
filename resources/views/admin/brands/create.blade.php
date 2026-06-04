<x-admin-layout>
    <x-slot name="pageTitle">Tambah Merek</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.brands.index') }}"
                class="text-warm-gray hover:text-warm-white transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Tambah Merek Baru</h2>
                <p class="mt-1 text-sm text-warm-gray">Isi formulir di bawah untuk menambahkan merek baru</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-dark-secondary rounded-lg shadow">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-warm-white mb-2">
                        Nama Merek <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: Maybelline"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-warm-white mb-2">
                        Deskripsi
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Deskripsi singkat tentang merek..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo Upload --}}
                <div>
                    <label for="logo" class="block text-sm font-medium text-warm-white mb-2">
                        Logo Merek
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                        <div
                            id="logo-preview"
                            class="hidden h-24 w-24 rounded-lg border-2 border-border-subtle overflow-hidden bg-dark-tertiary"
                        >
                            <img src="" alt="Preview" class="h-full w-full object-contain">
                        </div>
                        <div class="flex-1">
                            <input
                                type="file"
                                name="logo"
                                id="logo"
                                accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-warm-gray file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 @error('logo') border-red-500 @enderror"
                            >
                            <p class="mt-1 text-xs text-warm-gray">
                                Format: JPG, PNG, WebP. Maksimal 2MB.
                            </p>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Is Active --}}
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-border-subtle rounded"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-warm-white">
                        Aktifkan merek
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                    <a
                        href="{{ route('admin.brands.index') }}"
                        class="px-4 py-2 text-sm font-medium text-warm-white bg-dark-secondary border border-border-subtle rounded-lg hover:bg-dark-tertiary transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Simpan Merek
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Logo preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logo-preview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
</x-admin-layout>

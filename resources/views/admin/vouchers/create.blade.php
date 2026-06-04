<x-admin-layout>
    <x-slot name="pageTitle">Tambah Voucher</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.vouchers.index') }}"
                class="text-warm-gray hover:text-warm-white transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Tambah Voucher Baru</h2>
                <p class="mt-1 text-sm text-warm-gray">Isi formulir di bawah untuk menambahkan voucher baru</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-dark-secondary rounded-lg shadow">
            <form action="{{ route('admin.vouchers.store') }}" method="POST" class="p-6 space-y-6" x-data="voucherForm()">
                @csrf

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-warm-white mb-2">
                        Kode Voucher <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        value="{{ old('code') }}"
                        required
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('code') border-red-500 @enderror uppercase"
                        placeholder="Contoh: DISKON50"
                        maxlength="50"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-warm-gray">Kode akan otomatis diubah menjadi huruf kapital</p>
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-warm-white mb-2">
                        Tipe Diskon <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="type"
                        id="type"
                        required
                        x-model="type"
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('type') border-red-500 @enderror"
                    >
                        <option value="">Pilih tipe diskon</option>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Value --}}
                <div>
                    <label for="value" class="block text-sm font-medium text-warm-white mb-2">
                        Nilai Diskon <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="value"
                            id="value"
                            value="{{ old('value') }}"
                            required
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('value') border-red-500 @enderror"
                            :placeholder="type === 'percentage' ? 'Contoh: 10 (untuk 10%)' : 'Contoh: 50000'"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-warm-gray text-sm" x-text="type === 'percentage' ? '%' : 'Rp'"></span>
                        </div>
                    </div>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-warm-gray" x-show="type === 'percentage'">Maksimal 100%</p>
                </div>

                {{-- Minimum Purchase --}}
                <div>
                    <label for="minimum_purchase" class="block text-sm font-medium text-warm-white mb-2">
                        Minimum Pembelian
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-warm-gray text-sm">
                            Rp
                        </span>
                        <input
                            type="number"
                            name="minimum_purchase"
                            id="minimum_purchase"
                            value="{{ old('minimum_purchase', 0) }}"
                            min="0"
                            step="1000"
                            class="w-full pl-10 pr-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('minimum_purchase') border-red-500 @enderror"
                            placeholder="0"
                        >
                    </div>
                    @error('minimum_purchase')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-warm-gray">Kosongkan atau isi 0 jika tidak ada minimum pembelian</p>
                </div>

                {{-- Max Usage --}}
                <div>
                    <label for="max_usage" class="block text-sm font-medium text-warm-white mb-2">
                        Batas Penggunaan
                    </label>
                    <input
                        type="number"
                        name="max_usage"
                        id="max_usage"
                        value="{{ old('max_usage') }}"
                        min="1"
                        step="1"
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('max_usage') border-red-500 @enderror"
                        placeholder="Tidak terbatas"
                    >
                    @error('max_usage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-warm-gray">Kosongkan jika tidak ada batas penggunaan</p>
                </div>

                {{-- Expires At --}}
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-warm-white mb-2">
                        Tanggal Kedaluwarsa <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="expires_at"
                        id="expires_at"
                        value="{{ old('expires_at') }}"
                        required
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('expires_at') border-red-500 @enderror"
                    >
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                        Aktifkan voucher setelah dibuat
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                    <a
                        href="{{ route('admin.vouchers.index') }}"
                        class="px-4 py-2 text-sm font-medium text-warm-white bg-dark-secondary border border-border-subtle rounded-lg hover:bg-dark-tertiary transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Simpan Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function voucherForm() {
            return {
                type: '{{ old('type', '') }}'
            }
        }
    </script>
    @endpush
</x-admin-layout>

<x-admin-layout>
    <x-slot name="pageTitle">Edit Voucher</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.vouchers.index') }}"
                class="text-gray-600 hover:text-gray-900 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Voucher</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui informasi voucher {{ $voucher->code }}</p>
            </div>
        </div>

        {{-- Usage Info --}}
        @if ($voucher->used_count > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Voucher ini sudah digunakan {{ $voucher->used_count }} kali
                        </h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Perubahan pada voucher tidak akan mempengaruhi pesanan yang sudah menggunakan voucher ini.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="p-6 space-y-6" x-data="voucherForm()">
                @csrf
                @method('PUT')

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Voucher <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        value="{{ old('code', $voucher->code) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('code') border-red-500 @enderror uppercase"
                        placeholder="Contoh: DISKON50"
                        maxlength="50"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kode akan otomatis diubah menjadi huruf kapital</p>
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Diskon <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="type"
                        id="type"
                        required
                        x-model="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('type') border-red-500 @enderror"
                    >
                        <option value="">Pilih tipe diskon</option>
                        <option value="percentage" {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Value --}}
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                        Nilai Diskon <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="value"
                            id="value"
                            value="{{ old('value', $voucher->value) }}"
                            required
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('value') border-red-500 @enderror"
                            :placeholder="type === 'percentage' ? 'Contoh: 10 (untuk 10%)' : 'Contoh: 50000'"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm" x-text="type === 'percentage' ? '%' : 'Rp'"></span>
                        </div>
                    </div>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500" x-show="type === 'percentage'">Maksimal 100%</p>
                </div>

                {{-- Minimum Purchase --}}
                <div>
                    <label for="minimum_purchase" class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Pembelian
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            Rp
                        </span>
                        <input
                            type="number"
                            name="minimum_purchase"
                            id="minimum_purchase"
                            value="{{ old('minimum_purchase', $voucher->minimum_purchase) }}"
                            min="0"
                            step="1000"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('minimum_purchase') border-red-500 @enderror"
                            placeholder="0"
                        >
                    </div>
                    @error('minimum_purchase')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kosongkan atau isi 0 jika tidak ada minimum pembelian</p>
                </div>

                {{-- Max Usage --}}
                <div>
                    <label for="max_usage" class="block text-sm font-medium text-gray-700 mb-2">
                        Batas Penggunaan
                    </label>
                    <input
                        type="number"
                        name="max_usage"
                        id="max_usage"
                        value="{{ old('max_usage', $voucher->max_usage) }}"
                        min="1"
                        step="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('max_usage') border-red-500 @enderror"
                        placeholder="Tidak terbatas"
                    >
                    @error('max_usage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas penggunaan. Saat ini sudah digunakan {{ $voucher->used_count }} kali.</p>
                </div>

                {{-- Expires At --}}
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kedaluwarsa <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="expires_at"
                        id="expires_at"
                        value="{{ old('expires_at', $voucher->expires_at->format('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('expires_at') border-red-500 @enderror"
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
                        {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Voucher aktif
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a
                        href="{{ route('admin.vouchers.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function voucherForm() {
            return {
                type: '{{ old('type', $voucher->type) }}'
            }
        }
    </script>
    @endpush
</x-admin-layout>

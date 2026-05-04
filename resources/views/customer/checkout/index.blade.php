<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <a href="{{ route('cart.index') }}" class="hover:text-pink-600">Keranjang</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Checkout</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Checkout</h1>

            <form
                method="POST"
                action="{{ route('checkout.store') }}"
                x-data="checkoutForm()"
                @submit.prevent="submitOrder"
            >
                @csrf

                <div class="flex flex-col lg:flex-row gap-6">

                    {{-- ── Left Column: Address + Courier + Voucher ─────────── --}}
                    <div class="flex-1 space-y-6">

                        {{-- ── Shipping Address ─────────────────────────────── --}}
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Alamat Pengiriman
                            </h2>

                            @if ($addresses->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach ($addresses as $address)
                                        <label
                                            class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition"
                                            :class="selectedAddressId == {{ $address->id }}
                                                ? 'border-pink-500 bg-pink-50'
                                                : 'border-gray-200 hover:border-pink-300'"
                                        >
                                            <input
                                                type="radio"
                                                name="address_id"
                                                value="{{ $address->id }}"
                                                x-model="selectedAddressId"
                                                @change="onAddressChange"
                                                class="mt-1 text-pink-600 focus:ring-pink-500"
                                                {{ $address->is_default || $loop->first ? 'checked' : '' }}
                                            >
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-semibold text-sm text-gray-800">
                                                        {{ $address->recipient_name }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">{{ $address->phone }}</span>
                                                    @if ($address->is_default)
                                                        <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full font-medium">
                                                            Utama
                                                        </span>
                                                    @endif
                                                    @if ($address->label)
                                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                                            {{ $address->label }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $address->full_address }},
                                                    {{ $address->district }},
                                                    {{ $address->city }},
                                                    {{ $address->province }}
                                                    {{ $address->postal_code }}
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <a href="{{ route('customer.profile.edit') }}"
                                    class="mt-4 inline-flex items-center gap-1.5 text-sm text-pink-600 hover:text-pink-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah alamat baru
                                </a>
                            @else
                                {{-- No addresses yet --}}
                                <div class="text-center py-6 text-gray-500">
                                    <svg class="mx-auto w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <p class="text-sm mb-3">Anda belum memiliki alamat tersimpan.</p>
                                    <a href="{{ route('customer.profile.edit') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Alamat
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- ── Courier Selection ────────────────────────────── --}}
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.5 9h11L19 8" />
                                </svg>
                                Pilihan Kurir
                            </h2>

                            {{-- Loading state --}}
                            <div x-show="courierLoading" class="flex items-center gap-2 text-sm text-gray-500 py-4">
                                <svg class="animate-spin w-4 h-4 text-pink-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Memuat pilihan kurir...
                            </div>

                            {{-- Courier error --}}
                            <div x-show="courierError" x-text="courierError"
                                class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            </div>

                            {{-- Courier options (populated via AJAX) --}}
                            <div x-show="!courierLoading && courierOptions.length > 0" class="space-y-3">
                                <template x-for="option in courierOptions" :key="option.key">
                                    <label
                                        class="flex items-center justify-between p-4 border rounded-xl cursor-pointer transition"
                                        :class="selectedCourierKey === option.key
                                            ? 'border-pink-500 bg-pink-50'
                                            : 'border-gray-200 hover:border-pink-300'"
                                    >
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="radio"
                                                :value="option.key"
                                                x-model="selectedCourierKey"
                                                @change="onCourierChange(option)"
                                                class="text-pink-600 focus:ring-pink-500"
                                            >
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800"
                                                    x-text="option.courier_name + ' - ' + option.service"></p>
                                                <p class="text-xs text-gray-500" x-text="option.description"></p>
                                                <p class="text-xs text-gray-400" x-text="'Estimasi: ' + option.etd + ' hari'"></p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900"
                                            x-text="'Rp ' + formatNumber(option.cost)"></span>
                                    </label>
                                </template>
                            </div>

                            {{-- Placeholder when no address selected --}}
                            <div x-show="!courierLoading && courierOptions.length === 0 && !courierError"
                                class="text-sm text-gray-400 py-4 text-center">
                                Pilih alamat pengiriman terlebih dahulu untuk melihat pilihan kurir.
                            </div>

                            {{-- Hidden inputs for selected courier --}}
                            <input type="hidden" name="courier_name" :value="selectedCourierName">
                            <input type="hidden" name="courier_service" :value="selectedCourierService">
                            <input type="hidden" name="shipping_cost" :value="selectedShippingCost">
                        </div>

                        {{-- ── Voucher ──────────────────────────────────────── --}}
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Kode Voucher
                            </h2>

                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="voucher_code"
                                    x-model="voucherCode"
                                    placeholder="Masukkan kode voucher"
                                    class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent uppercase"
                                    :class="voucherApplied ? 'border-green-400 bg-green-50' : ''"
                                    :disabled="voucherApplied"
                                >
                                <button
                                    type="button"
                                    @click="applyVoucher"
                                    x-show="!voucherApplied"
                                    :disabled="!voucherCode || voucherLoading"
                                    class="px-4 py-2.5 bg-pink-600 text-white rounded-xl text-sm font-medium hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span x-show="!voucherLoading">Gunakan</span>
                                    <span x-show="voucherLoading">...</span>
                                </button>
                                <button
                                    type="button"
                                    @click="removeVoucher"
                                    x-show="voucherApplied"
                                    class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition"
                                >
                                    Hapus
                                </button>
                            </div>

                            <p x-show="voucherMessage" x-text="voucherMessage"
                                :class="voucherApplied ? 'text-green-600' : 'text-red-600'"
                                class="text-xs mt-2"></p>

                            @error('voucher_code')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Notes ────────────────────────────────────────── --}}
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Catatan Pesanan (Opsional)</h2>
                            <textarea
                                name="notes"
                                rows="3"
                                placeholder="Tambahkan catatan untuk penjual..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none"
                            >{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    {{-- ── Right Column: Order Summary ──────────────────────── --}}
                    <div class="lg:w-80 shrink-0">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>

                            {{-- Cart Items --}}
                            <div class="space-y-3 mb-4">
                                @foreach ($cartItems as $item)
                                    @php
                                        $primaryImage = $item->product?->images?->firstWhere('is_primary', true)
                                            ?? $item->product?->images?->first();
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 shrink-0">
                                            @if ($primaryImage)
                                                <img
                                                    src="{{ Storage::url($primaryImage->image_path) }}"
                                                    alt="{{ $item->product?->name }}"
                                                    class="w-full h-full object-cover"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-800 line-clamp-1">
                                                {{ $item->product?->name }}
                                            </p>
                                            @if ($item->variant)
                                                <p class="text-xs text-gray-400">
                                                    {{ $item->variant->name }}: {{ $item->variant->value }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-500">
                                                {{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-900 shrink-0">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Ongkos Kirim</span>
                                    <span x-text="selectedShippingCost > 0
                                        ? 'Rp ' + formatNumber(selectedShippingCost)
                                        : 'Pilih kurir'"
                                        class="font-medium"
                                        :class="selectedShippingCost > 0 ? 'text-gray-900' : 'text-gray-400'">
                                        Pilih kurir
                                    </span>
                                </div>
                                <div x-show="discountAmount > 0" class="flex justify-between text-green-600">
                                    <span>Diskon Voucher</span>
                                    <span x-text="'- Rp ' + formatNumber(discountAmount)" class="font-medium"></span>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 mt-3 pt-3">
                                <div class="flex justify-between font-bold text-gray-900">
                                    <span>Total</span>
                                    <span x-text="'Rp ' + formatNumber(grandTotal)">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button
                                type="submit"
                                :disabled="!canSubmit || submitting"
                                class="mt-6 w-full py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Memproses...' : 'Konfirmasi Pesanan'">Konfirmasi Pesanan</span>
                            </button>

                            <p x-show="!canSubmit && !submitting" class="text-xs text-center text-gray-400 mt-2">
                                Pilih alamat dan kurir untuk melanjutkan
                            </p>

                            <a href="{{ route('cart.index') }}"
                                class="mt-3 block w-full py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium text-center text-sm hover:bg-gray-50 transition">
                                Kembali ke Keranjang
                            </a>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function checkoutForm() {
            return {
                // Address
                selectedAddressId: {{ $addresses->where('is_default', true)->first()?->id ?? $addresses->first()?->id ?? 'null' }},

                // Courier
                courierLoading: false,
                courierError: '',
                courierOptions: [],
                selectedCourierKey: '',
                selectedCourierName: '',
                selectedCourierService: '',
                selectedShippingCost: 0,

                // Voucher
                voucherCode: '{{ old('voucher_code') }}',
                voucherLoading: false,
                voucherApplied: false,
                voucherMessage: '',
                discountAmount: 0,

                // Totals
                subtotal: {{ $subtotal }},

                // Form state
                submitting: false,

                get grandTotal() {
                    return Math.max(0, this.subtotal - this.discountAmount + this.selectedShippingCost);
                },

                get canSubmit() {
                    return this.selectedAddressId
                        && this.selectedCourierName
                        && this.selectedCourierService
                        && this.selectedShippingCost >= 0;
                },

                init() {
                    // Auto-load couriers if an address is already selected
                    if (this.selectedAddressId) {
                        this.loadCouriers(this.selectedAddressId);
                    }
                },

                onAddressChange() {
                    this.loadCouriers(this.selectedAddressId);
                },

                async loadCouriers(addressId) {
                    if (!addressId) return;

                    this.courierLoading = true;
                    this.courierError = '';
                    this.courierOptions = [];
                    this.selectedCourierKey = '';
                    this.selectedCourierName = '';
                    this.selectedCourierService = '';
                    this.selectedShippingCost = 0;

                    try {
                        const response = await fetch(`/shipping/cost?address_id=${addressId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.courierOptions = data.options ?? [];
                        } else {
                            this.courierError = data.message
                                || 'Gagal memuat pilihan kurir. Silakan coba lagi.';
                        }
                    } catch (err) {
                        this.courierError = 'Tidak dapat terhubung ke layanan pengiriman. Silakan coba lagi.';
                    } finally {
                        this.courierLoading = false;
                    }
                },

                onCourierChange(option) {
                    this.selectedCourierName    = option.courier_name;
                    this.selectedCourierService = option.service;
                    this.selectedShippingCost   = option.cost;
                },

                async applyVoucher() {
                    if (!this.voucherCode) return;

                    this.voucherLoading = true;
                    this.voucherMessage = '';

                    try {
                        const response = await fetch('/voucher/validate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                voucher_code: this.voucherCode,
                                subtotal: this.subtotal,
                            }),
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.voucherApplied  = true;
                            this.discountAmount  = data.discount_amount ?? 0;
                            this.voucherMessage  = data.message ?? 'Voucher berhasil diterapkan!';
                        } else {
                            this.voucherApplied = false;
                            this.discountAmount  = 0;
                            this.voucherMessage  = data.message ?? 'Kode voucher tidak valid.';
                        }
                    } catch (err) {
                        this.voucherMessage = 'Gagal memvalidasi voucher. Silakan coba lagi.';
                    } finally {
                        this.voucherLoading = false;
                    }
                },

                removeVoucher() {
                    this.voucherCode    = '';
                    this.voucherApplied = false;
                    this.discountAmount = 0;
                    this.voucherMessage = '';
                },

                async submitOrder(event) {
                    if (!this.canSubmit || this.submitting) return;

                    this.submitting = true;
                    event.target.submit();
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(value));
                },
            };
        }
    </script>
    @endpush
</x-app-layout>

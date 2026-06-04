<x-app-layout>
    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header: Gaya persis dengan halaman Katalog --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-border-subtle pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-gold-light">Checkout</span>
                        <span class="text-gold">Pesanan</span>
                    </h1>
                    <p class="text-warm-muted text-sm mt-1 font-medium">Konfirmasi alamat & metode pengiriman</p>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-dark-secondary border border-gold/30 text-gold text-[11px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                        Total: Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('checkout.store') }}"
                x-data="checkoutForm()"
                @submit.prevent="submitOrder"
            >
                @csrf

                <div class="flex flex-col lg:flex-row gap-6">

                    {{-- Kolom Kiri: Alamat + Kurir + Voucher + Catatan --}}
                    <div class="flex-1 space-y-6">

                        {{-- Alamat Pengiriman --}}
                        <div class="bg-dark-secondary rounded-xl border border-border-subtle shadow-sm p-6">
                            <h2 class="text-base font-bold text-warm-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                ? 'border-gold bg-gold/10'
                                                : 'border-border-subtle hover:border-gold/50'"
                                        >
                                            <input
                                                type="radio"
                                                name="address_id"
                                                value="{{ $address->id }}"
                                                x-model="selectedAddressId"
                                                @change="onAddressChange"
                                                class="mt-1 text-gold focus:ring-gold/40"
                                                {{ $address->is_default || $loop->first ? 'checked' : '' }}
                                            >
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-semibold text-sm text-warm-white">
                                                        {{ $address->recipient_name }}
                                                    </span>
                                                    <span class="text-xs text-warm-muted">{{ $address->phone }}</span>
                                                    @if ($address->is_default)
                                                        <span class="text-xs bg-[#FFE4EC] text-gold px-2 py-0.5 rounded-full font-medium">
                                                            Utama
                                                        </span>
                                                    @endif
                                                    @if ($address->label)
                                                        <span class="text-xs bg-dark-tertiary text-warm-gray px-2 py-0.5 rounded-full">
                                                            {{ $address->label }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-warm-gray mt-1">
                                                    {{ $address->full_address }},
                                                    {{ $address->district }},
                                                    {{ $address->city }},
                                                    {{ $address->province }}
                                                    {{ $address->postal_code }}
                                                </p>
                                            </div>
                                            <div class="flex gap-2 ml-2">
                                                <button
                                                    type="button"
                                                    @click.stop="openEditAddress({{ $address }})"
                                                    class="p-2 text-gold hover:bg-[#FFE4EC] rounded-lg transition"
                                                    title="Edit alamat"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click.stop="deleteAddress({{ $address->id }})"
                                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition"
                                                    title="Hapus alamat"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <button type="button" @click="openAddAddress()"
                                    class="mt-4 inline-flex items-center gap-1.5 text-sm text-gold hover:text-gold-dark font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah alamat baru
                                </button>
                            @else
                                <div class="text-center py-6 text-warm-muted">
                                    <svg class="mx-auto w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <p class="text-sm mb-3">Anda belum memiliki alamat tersimpan.</p>
                                    <button type="button" @click="openAddAddress()"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gold text-white rounded-lg text-sm font-medium hover:bg-[#d45a92] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Alamat
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Add Address Modal (Alpine controlled) --}}
                        <div x-show="showAddAddressModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
                            <div class="absolute inset-0 bg-black/40" @click="showAddAddressModal = false"></div>
                            <div class="relative bg-dark-secondary rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                                <h3 class="text-lg font-bold mb-3">Tambah Alamat Baru</h3>

                                <div x-show="addAddressError" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3 mb-3" x-text="addAddressError"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input type="text" placeholder="Label (mis. Rumah, Kantor)" x-model="newAddress.label" class="p-2 border rounded">
                                    <input type="text" placeholder="Nama Penerima" x-model="newAddress.recipient_name" class="p-2 border rounded">
                                    <input type="text" placeholder="No. HP" x-model="newAddress.phone" class="p-2 border rounded">

                                    <select x-model="newAddress.province_id" @change="onProvinceChange()"
                                        class="p-2 border rounded md:col-span-2">
                                        <option value="">Pilih Provinsi</option>
                                        <template x-for="prov in provinces" :key="prov.province_id">
                                            <option :value="prov.province_id" x-text="prov.province"></option>
                                        </template>
                                    </select>

                                    <select x-model="newAddress.city_id" @change="onCityChange()" class="p-2 border rounded">
                                        <option value="">Pilih Kota / Kabupaten</option>
                                        <template x-for="city in cities" :key="city.city_id">
                                            <option :value="city.city_id" :data-type="city.type" x-text="city.type + ' ' + city.city_name"></option>
                                        </template>
                                    </select>

                                    <select x-model="newAddress.district_id" @change="onDistrictChange($event)" class="p-2 border rounded">
                                        <option value="">Pilih Kecamatan</option>
                                        <template x-for="sub in subdistricts" :key="sub.subdistrict_id">
                                            <option :value="sub.subdistrict_id" :data-postal="sub.postal_code" :data-name="sub.subdistrict_name" x-text="sub.subdistrict_name"></option>
                                        </template>
                                    </select>

                                    <select x-model="newAddress.postal_code" class="p-2 border rounded">
                                        <option value="">Pilih Kode Pos</option>
                                        <template x-for="postal in postalCodes" :key="postal">
                                            <option :value="postal" x-text="postal"></option>
                                        </template>
                                    </select>

                                    <div class="text-xs text-warm-gray md:col-span-2" x-show="postalLoading">Memuat kode pos...</div>
                                    <div class="text-xs text-red-500 md:col-span-2" x-show="postalError" x-text="postalError"></div>

                                    <textarea placeholder="Alamat lengkap" x-model="newAddress.full_address" class="p-2 border rounded md:col-span-2"></textarea>
                                </div>

                                <div class="flex items-center gap-3 mt-4">
                                    <label class="inline-flex items-center gap-2"><input type="checkbox" x-model="newAddress.is_default"> Jadikan utama</label>
                                </div>

                                <div class="flex justify-end gap-3 mt-4">
                                    <button type="button" @click="showAddAddressModal = false" class="px-4 py-2 border rounded">Batal</button>
                                    <button type="button" @click="submitNewAddress()" :disabled="addAddressLoading" class="px-4 py-2 bg-gold text-white rounded">
                                        <span x-show="!addAddressLoading">Simpan</span>
                                        <span x-show="addAddressLoading">Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Address Modal (Alpine controlled) --}}
                        <div x-show="showEditAddressModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
                            <div class="absolute inset-0 bg-black/40" @click="showEditAddressModal = false"></div>
                            <div class="relative bg-dark-secondary rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                                <h3 class="text-lg font-bold mb-3">Edit Alamat</h3>

                                <div x-show="editAddressError" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3 mb-3" x-text="editAddressError"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <input type="text" placeholder="Label (mis. Rumah, Kantor)" x-model="editAddress.label" class="p-2 border rounded">
                                    <input type="text" placeholder="Nama Penerima" x-model="editAddress.recipient_name" class="p-2 border rounded">
                                    <input type="text" placeholder="No. HP" x-model="editAddress.phone" class="p-2 border rounded">

                                    <select x-model="editAddress.province_id" @change="onProvinceChangeEdit()"
                                        class="p-2 border rounded md:col-span-2">
                                        <option value="">Pilih Provinsi</option>
                                        <template x-for="prov in provinces" :key="prov.province_id">
                                            <option :value="prov.province_id" x-text="prov.province"></option>
                                        </template>
                                    </select>

                                    <select x-model="editAddress.city_id" @change="onCityChangeEdit()" class="p-2 border rounded">
                                        <option value="">Pilih Kota / Kabupaten</option>
                                        <template x-for="city in editCities" :key="city.city_id">
                                            <option :value="city.city_id" :data-type="city.type" x-text="city.type + ' ' + city.city_name"></option>
                                        </template>
                                    </select>

                                    <select x-model="editAddress.district_id" @change="onDistrictChangeEdit($event)" class="p-2 border rounded">
                                        <option value="">Pilih Kecamatan</option>
                                        <template x-for="sub in editSubdistricts" :key="sub.subdistrict_id">
                                            <option :value="sub.subdistrict_id" :data-postal="sub.postal_code" :data-name="sub.subdistrict_name" x-text="sub.subdistrict_name"></option>
                                        </template>
                                    </select>

                                    <select x-model="editAddress.postal_code" class="p-2 border rounded">
                                        <option value="">Pilih Kode Pos</option>
                                        <template x-for="postal in editPostalCodes" :key="postal">
                                            <option :value="postal" x-text="postal"></option>
                                        </template>
                                    </select>

                                    <textarea placeholder="Alamat lengkap" x-model="editAddress.full_address" class="p-2 border rounded md:col-span-2"></textarea>
                                </div>

                                <div class="flex items-center gap-3 mt-4">
                                    <label class="inline-flex items-center gap-2"><input type="checkbox" x-model="editAddress.is_default"> Jadikan utama</label>
                                </div>

                                <div class="flex justify-end gap-3 mt-4">
                                    <button type="button" @click="showEditAddressModal = false" class="px-4 py-2 border rounded">Batal</button>
                                    <button type="button" @click="submitEditAddress()" :disabled="editAddressLoading" class="px-4 py-2 bg-gold text-white rounded">
                                        <span x-show="!editAddressLoading">Perbarui</span>
                                        <span x-show="editAddressLoading">Memperbarui...</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Pilihan Kurir --}}
                        <div class="bg-dark-secondary rounded-xl border border-border-subtle shadow-sm p-6">
                            <h2 class="text-base font-bold text-warm-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.5 9h11L19 8" />
                                </svg>
                                Pilihan Kurir
                            </h2>

                            <div x-show="courierLoading" class="flex items-center gap-2 text-sm text-warm-muted py-4">
                                <svg class="animate-spin w-4 h-4 text-gold" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Memuat pilihan kurir...
                            </div>

                            <div x-show="courierError" x-text="courierError"
                                class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            </div>

                            <div x-show="!courierLoading && courierOptions.length > 0" class="space-y-3">
                                <template x-for="option in courierOptions" :key="option.key">
                                    <label
                                        class="flex items-center justify-between p-4 border rounded-xl cursor-pointer transition"
                                        :class="selectedCourierKey === option.key
                                            ? 'border-gold bg-gold/10'
                                            : 'border-border-subtle hover:border-gold/50'"
                                    >
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="radio"
                                                :value="option.key"
                                                x-model="selectedCourierKey"
                                                @change="onCourierChange(option)"
                                                class="text-gold focus:ring-gold/40"
                                            >
                                            <div>
                                                <p class="text-sm font-semibold text-warm-white"
                                                    x-text="option.courier_name + ' - ' + option.service"></p>
                                                <p class="text-xs text-warm-muted" x-text="option.description"></p>
                                                <p class="text-xs text-warm-muted" x-text="'Estimasi: ' + option.etd + ' hari'"></p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-warm-white"
                                            x-text="'Rp ' + formatNumber(option.cost)"></span>
                                    </label>
                                </template>
                            </div>

                            <div x-show="!courierLoading && courierOptions.length === 0 && !courierError"
                                class="text-sm text-warm-muted py-4 text-center">
                                Pilih alamat pengiriman terlebih dahulu untuk melihat pilihan kurir.
                            </div>

                            <input type="hidden" name="courier_name" :value="selectedCourierName">
                            <input type="hidden" name="courier_service" :value="selectedCourierService">
                            <input type="hidden" name="shipping_cost" :value="selectedShippingCost">
                        </div>

                        {{-- Voucher --}}
                        <div class="bg-dark-secondary rounded-xl border border-border-subtle shadow-sm p-6">
                            <h2 class="text-base font-bold text-warm-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    class="flex-1 border border-border-subtle rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-transparent uppercase"
                                    :class="voucherApplied ? 'border-green-400 bg-green-50' : ''"
                                    :disabled="voucherApplied"
                                >
                                <button
                                    type="button"
                                    @click="applyVoucher"
                                    x-show="!voucherApplied"
                                    :disabled="!voucherCode || voucherLoading"
                                    class="px-4 py-2.5 bg-gold text-white rounded-xl text-sm font-medium hover:bg-[#d45a92] transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span x-show="!voucherLoading">Gunakan</span>
                                    <span x-show="voucherLoading">...</span>
                                </button>
                                <button
                                    type="button"
                                    @click="removeVoucher"
                                    x-show="voucherApplied"
                                    class="px-4 py-2.5 border border-border-subtle text-warm-gray rounded-xl text-sm font-medium hover:bg-dark-tertiary transition"
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

                        {{-- Catatan Pesanan --}}
                        <div class="bg-dark-secondary rounded-xl border border-border-subtle shadow-sm p-6">
                            <h2 class="text-base font-bold text-warm-white mb-4">Catatan Pesanan (Opsional)</h2>
                            <textarea
                                name="notes"
                                rows="3"
                                placeholder="Tambahkan catatan untuk penjual..."
                                class="w-full border border-border-subtle rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-transparent resize-none"
                            >{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    {{-- Kolom Kanan: Ringkasan Pesanan --}}
                    <div class="lg:w-80 shrink-0">
                        <div class="bg-dark-secondary rounded-xl border border-border-subtle shadow-sm p-6 sticky top-6">
                            <h2 class="text-base font-bold text-warm-white mb-4">Ringkasan Pesanan</h2>

                            {{-- Item Keranjang --}}
                            <div class="space-y-3 mb-4">
                                @foreach ($cartItems as $item)
                                    @php
                                        $primaryImage = $item->product?->images?->firstWhere('is_primary', true)
                                            ?? $item->product?->images?->first();
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-dark-tertiary shrink-0">
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
                                            <p class="text-xs font-medium text-warm-white line-clamp-1">
                                                {{ $item->product?->name }}
                                            </p>
                                            @if ($item->variant)
                                                <p class="text-xs text-warm-muted">
                                                    {{ $item->variant->name }}: {{ $item->variant->value }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-warm-muted">
                                                {{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <p class="text-xs font-semibold text-warm-white shrink-0">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-gold/30/30 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between text-warm-gray">
                                    <span>Subtotal</span>
                                    <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-warm-gray">
                                    <span>Ongkos Kirim</span>
                                    <span x-text="selectedShippingCost > 0
                                        ? 'Rp ' + formatNumber(selectedShippingCost)
                                        : 'Pilih kurir'"
                                        class="font-medium"
                                        :class="selectedShippingCost > 0 ? 'text-warm-white' : 'text-warm-muted'">
                                        Pilih kurir
                                    </span>
                                </div>
                                <div x-show="discountAmount > 0" class="flex justify-between text-green-600">
                                    <span>Diskon Voucher</span>
                                    <span x-text="'- Rp ' + formatNumber(discountAmount)" class="font-medium"></span>
                                </div>
                            </div>

                            <div class="border-t border-gold/30/30 mt-3 pt-3">
                                <div class="flex justify-between font-bold text-warm-white">
                                    <span>Total</span>
                                    <span x-text="'Rp ' + formatNumber(grandTotal)">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="!canSubmit || submitting"
                                class="mt-6 w-full py-3 bg-gold text-white rounded-xl font-semibold hover:bg-[#d45a92] transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Memproses...' : 'Konfirmasi Pesanan'">Konfirmasi Pesanan</span>
                            </button>

                            <p x-show="!canSubmit && !submitting" class="text-xs text-center text-warm-muted mt-2">
                                Pilih alamat dan kurir untuk melanjutkan
                            </p>

                            <a href="{{ route('cart.index') }}"
                                class="mt-3 block w-full py-2.5 border border-border-subtle text-gold rounded-xl font-medium text-center text-sm hover:bg-dark-tertiary transition">
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
                selectedAddressId: {{ $addresses->where('is_default', true)->first()?->id ?? $addresses->first()?->id ?? 'null' }},
                // Add-address modal state
                showAddAddressModal: false,
                provinces: [],
                cities: [],
                subdistricts: [],
                postalCodes: [],
                newAddress: {
                    label: '',
                    recipient_name: '',
                    phone: '',
                    province_id: '',
                    province: '',
                    city_id: '',
                    city: '',
                    district_id: '',
                    district: '',
                    postal_code: '',
                    full_address: '',
                    is_default: false,
                },
                addAddressLoading: false,
                addAddressError: '',
                postalLoading: false,
                postalError: '',
                
                // Edit-address modal state
                showEditAddressModal: false,
                editAddressId: null,
                editAddress: {
                    label: '',
                    recipient_name: '',
                    phone: '',
                    province_id: '',
                    province: '',
                    city_id: '',
                    city: '',
                    district_id: '',
                    district: '',
                    postal_code: '',
                    full_address: '',
                    is_default: false,
                },
                editCities: [],
                editSubdistricts: [],
                editPostalCodes: [],
                editAddressLoading: false,
                editAddressError: '',
                
                courierLoading: false,
                courierError: '',
                courierOptions: [],
                selectedCourierKey: '',
                selectedCourierName: '',
                selectedCourierService: '',
                selectedShippingCost: 0,
                voucherCode: '{{ old('voucher_code') }}',
                voucherLoading: false,
                voucherApplied: false,
                voucherMessage: '',
                discountAmount: 0,
                subtotal: {{ $subtotal }},
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
                    if (this.selectedAddressId) {
                        this.loadCouriers(this.selectedAddressId);
                    }
                },

                openAddAddress() {
                    this.addAddressError = '';
                    this.showAddAddressModal = true;
                    if (!this.provinces.length) {
                        this.loadProvinces();
                    }
                },

                openEditAddress(address) {
                    this.editAddressError = '';
                    this.editAddressId = address.id;
                    this.editAddress = {
                        label: address.label || '',
                        recipient_name: address.recipient_name,
                        phone: address.phone,
                        province_id: address.province_id || '',
                        province: address.province,
                        city_id: address.city_id || '',
                        city: address.city,
                        district_id: address.district_id || '',
                        district: address.district,
                        postal_code: address.postal_code,
                        full_address: address.full_address,
                        is_default: address.is_default,
                    };
                    this.showEditAddressModal = true;
                    if (!this.provinces.length) {
                        this.loadProvinces().then(() => this.onProvinceChangeEdit());
                    } else {
                        this.onProvinceChangeEdit();
                    }
                },

                async submitEditAddress() {
                    this.editAddressError = '';
                    if (this.editAddressLoading) return;
                    if (!this.editAddress.recipient_name || !this.editAddress.phone || !this.editAddress.full_address) {
                        this.editAddressError = 'Mohon lengkapi nama, nomor telepon, dan alamat lengkap.';
                        return;
                    }

                    this.editAddressLoading = true;

                    try {
                        const payload = {
                            label: this.editAddress.label,
                            recipient_name: this.editAddress.recipient_name,
                            phone: this.editAddress.phone,
                            province_id: this.editAddress.province_id || null,
                            province: this.editAddress.province || '',
                            city_id: this.editAddress.city_id || null,
                            city: this.editAddress.city || '',
                            district_id: this.editAddress.district_id || null,
                            district: this.editAddress.district || '',
                            postal_code: this.editAddress.postal_code,
                            full_address: this.editAddress.full_address,
                            is_default: this.editAddress.is_default ? 1 : 0,
                        };

                        const response = await fetch(`/customer/addresses/${this.editAddressId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            window.location.reload();
                        } else {
                            this.editAddressError = data.message || 'Gagal memperbarui alamat.';
                        }
                    } catch (err) {
                        this.editAddressError = 'Tidak dapat memperbarui alamat. Silakan coba lagi.';
                    } finally {
                        this.editAddressLoading = false;
                    }
                },

                async deleteAddress(addressId) {
                    if (!confirm('Apakah Anda yakin ingin menghapus alamat ini?')) return;

                    try {
                        const response = await fetch(`/customer/addresses/${addressId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal menghapus alamat.');
                        }
                    } catch (err) {
                        alert('Tidak dapat menghapus alamat. Silakan coba lagi.');
                    }
                },

                async submitNewAddress() {
                    this.addAddressError = '';
                    if (this.addAddressLoading) return;
                    // basic client-side check
                    if (!this.newAddress.recipient_name || !this.newAddress.phone || !this.newAddress.full_address) {
                        this.addAddressError = 'Mohon lengkapi nama, nomor telepon, dan alamat lengkap.';
                        return;
                    }

                    this.addAddressLoading = true;

                    try {
                        const payload = {
                            label: this.newAddress.label,
                            recipient_name: this.newAddress.recipient_name,
                            phone: this.newAddress.phone,
                            province_id: this.newAddress.province_id || null,
                            province: this.newAddress.province || '',
                            city_id: this.newAddress.city_id || null,
                            city: this.newAddress.city || '',
                            district_id: this.newAddress.district_id || null,
                            district: this.newAddress.district || '',
                            postal_code: this.newAddress.postal_code,
                            full_address: this.newAddress.full_address,
                            is_default: this.newAddress.is_default ? 1 : 0,
                        };

                            const response = await fetch('/customer/addresses', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify(payload),
                            });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Refresh to load updated addresses and select the new one
                            window.location.reload();
                        } else {
                            this.addAddressError = data.message || 'Gagal menambahkan alamat.';
                        }
                    } catch (err) {
                        this.addAddressError = 'Tidak dapat menyimpan alamat. Silakan coba lagi.';
                    } finally {
                        this.addAddressLoading = false;
                    }
                },

                async loadProvinces() {
                    try {
                        const res = await fetch('/customer/rajaongkir/provinces', { headers: { Accept: 'application/json' } });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.provinces = data.data;
                        }
                    } catch (e) {
                        // ignore
                    }
                },

                async onProvinceChange() {
                    this.cities = [];
                    this.subdistricts = [];
                    this.newAddress.province = '';
                    this.newAddress.city_id = '';
                    this.newAddress.city = '';
                    this.newAddress.district_id = '';
                    this.newAddress.district = '';
                    this.newAddress.postal_code = '';
                    if (!this.newAddress.province_id) return;
                    try {
                        const res = await fetch(`/customer/rajaongkir/cities?province_id=${this.newAddress.province_id}`, { headers: { Accept: 'application/json' } });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.cities = data.data;
                            const selected = this.provinces.find(p => String(p.province_id) === String(this.newAddress.province_id));
                            this.newAddress.province = selected ? selected.province : '';
                        }
                    } catch (e) {}
                },

                async onProvinceChangeEdit() {
                    this.editCities = [];
                    this.editSubdistricts = [];
                    this.editAddress.province = '';
                    this.editAddress.city_id = '';
                    this.editAddress.city = '';
                    this.editAddress.district_id = '';
                    this.editAddress.district = '';
                    this.editAddress.postal_code = '';
                    if (!this.editAddress.province_id) return;
                    try {
                        const res = await fetch(`/customer/rajaongkir/cities?province_id=${this.editAddress.province_id}`, { headers: { Accept: 'application/json' } });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.editCities = data.data;
                            const selected = this.provinces.find(p => String(p.province_id) === String(this.editAddress.province_id));
                            this.editAddress.province = selected ? selected.province : '';
                        }
                    } catch (e) {}
                },

                async onCityChange() {
                    this.subdistricts = [];
                    this.postalCodes = [];
                    this.newAddress.city = '';
                    this.newAddress.district_id = '';
                    this.newAddress.district = '';
                    this.newAddress.postal_code = '';
                    if (!this.newAddress.city_id) return;
                    try {
                        const res = await fetch(`/customer/rajaongkir/subdistricts?city_id=${this.newAddress.city_id}`, { headers: { Accept: 'application/json' } });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.subdistricts = data.data;
                            const selected = this.cities.find(c => String(c.city_id) === String(this.newAddress.city_id));
                            this.newAddress.city = selected ? (selected.type + ' ' + selected.city_name) : '';
                        }
                    } catch (e) {}
                },

                async onCityChangeEdit() {
                    this.editSubdistricts = [];
                    this.editPostalCodes = [];
                    this.editAddress.city = '';
                    this.editAddress.district_id = '';
                    this.editAddress.district = '';
                    this.editAddress.postal_code = '';
                    if (!this.editAddress.city_id) return;
                    try {
                        const res = await fetch(`/customer/rajaongkir/subdistricts?city_id=${this.editAddress.city_id}`, { headers: { Accept: 'application/json' } });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.editSubdistricts = data.data;
                            const selected = this.editCities.find(c => String(c.city_id) === String(this.editAddress.city_id));
                            this.editAddress.city = selected ? (selected.type + ' ' + selected.city_name) : '';
                        }
                    } catch (e) {}
                },

                async onDistrictChange(event) {
                    const sel = event?.target;
                    if (!sel) return;
                    const opt = sel.options[sel.selectedIndex];
                    const postal = opt?.dataset?.postal;
                    const districtName = opt?.dataset?.name;
                    if (districtName) this.newAddress.district = districtName;
                    if (postal) this.newAddress.postal_code = postal;

                    await this.loadPostalCodes();
                },

                async loadPostalCodes() {
                    this.postalCodes = [];
                    this.postalError = '';

                    if (!this.newAddress.district_id) return;

                    this.postalLoading = true;

                    try {
                        const res = await fetch(`/customer/rajaongkir/postal-codes?district_id=${this.newAddress.district_id}`, {
                            headers: { Accept: 'application/json' },
                        });
                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.postalCodes = data.data ?? [];

                            if (this.postalCodes.length === 1) {
                                this.newAddress.postal_code = this.postalCodes[0];
                            } else if (this.postalCodes.length > 1 && !this.postalCodes.includes(this.newAddress.postal_code)) {
                                this.newAddress.postal_code = '';
                            }
                        } else {
                            this.postalError = data.message || 'Gagal memuat kode pos.';
                        }
                    } catch (e) {
                        this.postalError = 'Gagal memuat kode pos.';
                    } finally {
                        this.postalLoading = false;
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
                            // Normalize options and add stable keys for radio selection.
                            const rawOptions = data.data ?? data.options ?? [];
                            this.courierOptions = rawOptions.map((option, index) => ({
                                ...option,
                                key: option.key ?? `${option.courier_name ?? 'courier'}-${option.service ?? 'service'}-${option.cost ?? 0}-${index}`,
                            }));
                        } else {
                            this.courierError = data.message || 'Gagal memuat pilihan kurir. Silakan coba lagi.';
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
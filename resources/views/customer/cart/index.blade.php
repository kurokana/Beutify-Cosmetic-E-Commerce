<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Keranjang Belanja</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
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

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Keranjang Belanja</h1>

            @if ($cartItems->isEmpty())
                {{-- Empty Cart State --}}
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Keranjang Anda kosong</h2>
                    <p class="text-sm text-gray-400 mb-6">Tambahkan produk ke keranjang untuk mulai berbelanja.</p>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="flex flex-col lg:flex-row gap-6">

                    {{-- ── Cart Items ──────────────────────────────────────── --}}
                    <div class="flex-1 space-y-4">
                        @foreach ($cartItems as $item)
                            @php
                                $primaryImage = $item->product?->images?->firstWhere('is_primary', true)
                                    ?? $item->product?->images?->first();
                            @endphp

                            {{-- Cart Item Row — Alpine.js component for real-time update --}}
                            <div
                                x-data="cartItem({
                                    itemId: {{ $item->id }},
                                    quantity: {{ $item->quantity }},
                                    unitPrice: {{ $item->unit_price }},
                                    updateUrl: '{{ route('cart.update', $item->id) }}',
                                    destroyUrl: '{{ route('cart.destroy', $item->id) }}',
                                    csrfToken: '{{ csrf_token() }}'
                                })"
                                x-show="!removed"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="bg-white rounded-xl shadow-sm overflow-hidden"
                            >
                                <div class="flex gap-4 p-4">
                                    {{-- Product Image --}}
                                    <a href="{{ $item->product ? route('catalog.show', $item->product->slug) : '#' }}"
                                        class="shrink-0 w-24 h-24 rounded-lg overflow-hidden bg-gray-50">
                                        @if ($primaryImage)
                                            <img
                                                src="{{ Storage::url($primaryImage->image_path) }}"
                                                alt="{{ $item->product?->name }}"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>

                                    {{-- Product Info --}}
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ $item->product ? route('catalog.show', $item->product->slug) : '#' }}"
                                            class="text-sm font-semibold text-gray-800 hover:text-pink-600 line-clamp-2 leading-snug">
                                            {{ $item->product?->name ?? 'Produk tidak tersedia' }}
                                        </a>

                                        {{-- Variant --}}
                                        @if ($item->variant)
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->variant->name }}: {{ $item->variant->value }}
                                            </p>
                                        @endif

                                        {{-- Unit Price --}}
                                        <p class="text-sm font-medium text-gray-700 mt-1">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </p>

                                        {{-- Error message --}}
                                        <p x-show="errorMessage" x-text="errorMessage"
                                            class="text-xs text-red-600 mt-1"></p>
                                    </div>

                                    {{-- Quantity & Subtotal --}}
                                    <div class="flex flex-col items-end justify-between shrink-0">
                                        {{-- Subtotal --}}
                                        <p class="text-sm font-bold text-gray-900">
                                            Rp <span x-text="subtotalFormatted">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </p>

                                        {{-- Quantity Controls --}}
                                        <div class="flex items-center gap-2 mt-2">
                                            <button
                                                @click="decrement()"
                                                :disabled="loading || quantity <= 1"
                                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600
                                                    hover:border-pink-500 hover:text-pink-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>

                                            <input
                                                type="number"
                                                x-model.number="quantity"
                                                @change="updateQuantity()"
                                                :disabled="loading"
                                                min="1"
                                                class="w-12 text-center text-sm font-medium border border-gray-300 rounded-lg py-1
                                                    focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent
                                                    disabled:opacity-40"
                                            >

                                            <button
                                                @click="increment()"
                                                :disabled="loading"
                                                class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600
                                                    hover:border-pink-500 hover:text-pink-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- Delete Button --}}
                                        <button
                                            @click="removeItem()"
                                            :disabled="loading"
                                            class="mt-2 text-xs text-red-500 hover:text-red-700 transition disabled:opacity-40"
                                            title="Hapus item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Loading overlay --}}
                                <div x-show="loading" class="absolute inset-0 bg-white/60 flex items-center justify-center rounded-xl">
                                    <svg class="animate-spin w-5 h-5 text-pink-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ── Order Summary ───────────────────────────────────── --}}
                    <div class="lg:w-80 shrink-0">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal ({{ $cartItems->count() }} produk)</span>
                                    <span id="cart-total" class="font-medium text-gray-900">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-gray-400 text-xs">
                                    <span>Ongkos kirim</span>
                                    <span>Dihitung saat checkout</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 mt-4 pt-4">
                                <div class="flex justify-between font-bold text-gray-900">
                                    <span>Total</span>
                                    <span id="cart-total-bold">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.index') }}"
                                class="mt-6 block w-full py-3 bg-pink-600 text-white rounded-xl font-semibold text-center hover:bg-pink-700 transition">
                                Lanjut ke Checkout
                            </a>

                            <a href="{{ route('catalog.index') }}"
                                class="mt-3 block w-full py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium text-center text-sm hover:bg-gray-50 transition">
                                Lanjut Belanja
                            </a>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        function cartItem(config) {
            return {
                itemId: config.itemId,
                quantity: config.quantity,
                unitPrice: config.unitPrice,
                updateUrl: config.updateUrl,
                destroyUrl: config.destroyUrl,
                csrfToken: config.csrfToken,
                loading: false,
                removed: false,
                errorMessage: '',

                get subtotalFormatted() {
                    const subtotal = this.unitPrice * this.quantity;
                    return new Intl.NumberFormat('id-ID').format(subtotal);
                },

                increment() {
                    this.quantity++;
                    this.updateQuantity();
                },

                decrement() {
                    if (this.quantity > 1) {
                        this.quantity--;
                        this.updateQuantity();
                    }
                },

                async updateQuantity() {
                    if (this.quantity < 1) {
                        this.quantity = 1;
                        return;
                    }

                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch(this.updateUrl, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ quantity: this.quantity }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update cart total in the summary panel
                            updateCartTotal(data.total_formatted);
                        } else {
                            this.errorMessage = data.message || 'Gagal memperbarui jumlah.';
                        }
                    } catch (err) {
                        this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },

                async removeItem() {
                    if (!confirm('Hapus item ini dari keranjang?')) return;

                    this.loading = true;

                    try {
                        const response = await fetch(this.destroyUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.removed = true;
                            updateCartTotal(data.total_formatted);
                        }
                    } catch (err) {
                        this.errorMessage = 'Gagal menghapus item. Silakan coba lagi.';
                        this.loading = false;
                    }
                },
            };
        }

        function updateCartTotal(formattedTotal) {
            const totalEl = document.getElementById('cart-total');
            const totalBoldEl = document.getElementById('cart-total-bold');
            if (totalEl) totalEl.textContent = formattedTotal;
            if (totalBoldEl) totalBoldEl.textContent = formattedTotal;
        }
    </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <style>
        /* Menghilangkan panah (spinners) di input number agar bersih */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('catalog.index') }}" class="hover:text-gold text-warm-muted transition">Katalog</a>
            <span class="text-border-subtle">/</span>
            <span class="text-gold font-medium">Keranjang Belanja</span>
        </nav>
    </x-slot>

    <div class="relative py-8 bg-dark-primary min-h-screen overflow-hidden">
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-end justify-between mb-6 border-b border-border-subtle pb-4">
                <h1 class="text-2xl font-bold tracking-tight">
                    <span class="text-gold text-glow">Shopping</span> 
                    <span class="text-warm-white">Cart</span>
                </h1>
                
                <span class="px-3 py-1 bg-dark-secondary border border-gold/30 text-gold text-[10px] font-black uppercase tracking-widest rounded-lg shadow-gold-sm">
                    {{ $cartItems->count() }} Item Terpilih
                </span>
            </div>

            @if ($cartItems->isEmpty())
                <div class="bg-dark-secondary rounded-[2rem] border border-border-subtle shadow-dark-card p-16 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gold/10 flex items-center justify-center text-gold">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-warm-white mb-2">Wah, keranjangmu masih kosong</h2>
                    <p class="text-warm-muted mb-8 max-w-xs mx-auto text-sm">Yuk, intip koleksi skincare dan makeup terbaru kami!</p>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center gap-2 px-8 py-3 btn-gold rounded-full font-bold text-sm">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="flex flex-col lg:flex-row gap-8">
                    
                    <div class="flex-1 space-y-4">
                        @foreach ($cartItems as $item)
                            <div 
                                x-data="cartItem({
                                    itemId: {{ $item->id }},
                                    quantity: {{ $item->quantity }},
                                    unitPrice: {{ $item->unit_price }},
                                    updateUrl: '{{ route('cart.update', $item->id) }}',
                                    destroyUrl: '{{ route('cart.destroy', $item->id) }}',
                                    csrfToken: '{{ csrf_token() }}'
                                })"
                                class="group relative bg-dark-secondary rounded-[1.5rem] border border-border-subtle shadow-dark-card p-4 flex flex-col sm:flex-row gap-5 items-center 
                                       hover:shadow-[0_15px_35px_rgba(200,149,108,0.06)] hover:border-gold/25 hover:-translate-y-1 transition-all duration-300"
                            >
                                <div class="relative w-24 h-24 shrink-0 overflow-hidden rounded-xl bg-dark-tertiary border border-border-subtle shadow-sm p-1">
                                    @php
                                        $primaryImage = $item->product?->images?->firstWhere('is_primary', true) ?? $item->product?->images?->first();
                                    @endphp
                                    @if ($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain rounded-lg">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-warm-muted">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0 text-center sm:text-left">
                                    <p class="text-[9px] text-gold font-black uppercase tracking-widest mb-1">
                                        {{ $item->product->brand->name ?? 'Beauty Brand' }}
                                    </p>
                                    <h3 class="text-sm font-bold text-warm-white truncate mb-1">
                                        {{ $item->product->name }}
                                    </h3>
                                    <p class="text-xs font-extrabold text-warm-white mb-3">
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>

                                    <div class="flex items-center justify-center sm:justify-start gap-4">
                                        <div class="flex items-center bg-dark-tertiary border border-border-subtle rounded-full p-1 shadow-sm">
                                            <button @click="decrement()" :disabled="loading" class="w-7 h-7 flex items-center justify-center rounded-full bg-dark-secondary text-warm-gray hover:text-gold transition-all disabled:opacity-50">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" /></svg>
                                            </button>
                                            
                                            <input type="number" x-model.number="quantity" @change="updateQuantity()" 
                                                class="w-8 text-center bg-transparent border-none focus:ring-0 text-xs font-bold text-warm-white px-0">
                                            
                                            <button @click="increment()" :disabled="loading" class="w-7 h-7 flex items-center justify-center rounded-full bg-dark-secondary text-warm-gray hover:text-gold transition-all disabled:opacity-50">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                            </button>
                                        </div>

                                        <button @click="removeItem()" class="text-[10px] font-bold text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                <div class="sm:text-right border-t sm:border-t-0 sm:border-l border-dashed border-border-subtle pt-3 sm:pt-0 sm:pl-6">
                                    <p class="text-[9px] font-bold text-warm-muted uppercase tracking-tighter">Subtotal</p>
                                    <p class="text-base font-black text-gold">
                                        Rp <span x-text="subtotalFormatted"></span>
                                    </p>
                                </div>

                                <div x-show="loading" class="absolute inset-0 bg-dark-secondary/60 backdrop-blur-[1px] z-30 flex items-center justify-center rounded-[1.5rem]">
                                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-gold border-t-transparent"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="lg:w-[350px]">
                        <div class="bg-dark-secondary rounded-[1.5rem] border border-border-subtle shadow-dark-card p-6 sticky top-24">
                            <h2 class="text-base font-black text-warm-white mb-5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Ringkasan Belanja
                            </h2>
                            
                            <div class="space-y-3 mb-6 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-warm-gray font-medium">Subtotal Produk</span>
                                    <span class="text-warm-white font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-warm-gray font-medium">Biaya Layanan</span>
                                    <span class="text-warm-white font-bold">Rp 0</span>
                                </div>
                                <div class="pt-3 border-t border-dashed border-border-subtle flex justify-between items-end">
                                    <span class="text-sm font-bold text-warm-white">Total Harga</span>
                                    <span class="text-xl font-black text-gold text-glow">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.index') }}" 
                                class="block w-full py-3.5 btn-gold text-center text-xs font-black rounded-full uppercase tracking-widest">
                                Checkout
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
                loading: false,

                get subtotalFormatted() {
                    return new Intl.NumberFormat('id-ID').format(this.quantity * this.unitPrice);
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
                    if (this.quantity < 1) this.quantity = 1;
                    this.loading = true;
                    try {
                        const response = await fetch(config.updateUrl, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ quantity: this.quantity })
                        });
                        if (response.ok) window.location.reload();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                async removeItem() {
                    if (!confirm('Hapus produk ini dari keranjang?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch(config.destroyUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': config.csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) window.location.reload();
                    } catch (e) {
                        console.error(e);
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
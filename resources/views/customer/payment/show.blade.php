<x-app-layout>
    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header seperti halaman Katalog --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-border-subtle pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-gold-light">Pembayaran</span>
                        <span class="text-gold">Pesanan</span>
                    </h1>
                    <p class="text-warm-muted text-sm mt-1 font-medium">{{ $order->order_number }}</p>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-dark-secondary border border-gold/30 text-gold text-[11px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                        Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">

                {{-- Ringkasan Pesanan --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Ringkasan Pesanan
                    </h2>

                    {{-- Items --}}
                    <div class="divide-y divide-border-subtle mb-4">
                        @foreach ($order->items as $item)
                            <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-warm-white">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-warm-muted mt-0.5">Varian: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-warm-muted mt-0.5">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-sm font-extrabold text-warm-white shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Detail harga --}}
                    <div class="border-t border-gold/30/30 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-warm-gray">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-warm-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-warm-gray">
                            <span>Ongkos Kirim ({{ strtoupper($order->courier_name) }} {{ $order->courier_service }})</span>
                            <span class="font-bold text-warm-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>
                                    Diskon Voucher
                                    @if ($order->voucher)
                                        <span class="text-xs text-warm-muted">({{ $order->voucher->code }})</span>
                                    @endif
                                </span>
                                <span class="font-bold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gold/30/30 pt-3 mt-1 flex justify-between font-black text-warm-white text-base">
                            <span>Total Pembayaran</span>
                            <span class="text-gold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Batas Waktu Pembayaran --}}
                @if ($order->payment?->expired_at)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-bold text-amber-800">Batas Waktu Pembayaran</p>
                            <p class="text-amber-700 mt-0.5">
                                {{ $order->payment->expired_at->translatedFormat('d F Y, H:i') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Metode Pembayaran Tersedia --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Metode Pembayaran Tersedia
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-center text-warm-gray">
                        <div class="p-3 bg-dark-tertiary rounded-lg border border-gold/30/30">
                            <p class="font-bold text-warm-white mb-1">Virtual Account</p>
                            <p>BCA, BNI, BRI,<br>Mandiri, Permata</p>
                        </div>
                        <div class="p-3 bg-dark-tertiary rounded-lg border border-gold/30/30">
                            <p class="font-bold text-warm-white mb-1">E-Wallet</p>
                            <p>GoPay, OVO,<br>Dana, ShopeePay</p>
                        </div>
                        <div class="p-3 bg-dark-tertiary rounded-lg border border-gold/30/30">
                            <p class="font-bold text-warm-white mb-1">Kartu Kredit</p>
                            <p>Visa, Mastercard,<br>JCB, Amex</p>
                        </div>
                        <div class="p-3 bg-dark-tertiary rounded-lg border border-gold/30/30">
                            <p class="font-bold text-warm-white mb-1">QRIS</p>
                            <p>Semua aplikasi<br>pembayaran QRIS</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Bayar --}}
                <div x-data="paymentHandler()" class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <div x-show="errorMessage" x-text="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"></div>

                    <button type="button" @click="pay" :disabled="loading"
                        class="w-full py-4 bg-gold text-white rounded-xl font-extrabold text-base hover:bg-[#d45a92] transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3 shadow-md">
                        <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span x-text="loading ? 'Memuat...' : 'Bayar Sekarang'">Bayar Sekarang</span>
                    </button>

                    <p class="text-xs text-center text-warm-muted mt-3">
                        Pembayaran diproses secara aman melalui Midtrans
                    </p>

                    <button type="button" @click="refresh" :disabled="loading"
                        class="mt-3 w-full py-3 border border-gold/30 text-warm-white rounded-xl font-semibold text-sm hover:bg-dark-tertiary hover:border-gold transition disabled:opacity-60 disabled:cursor-not-allowed">
                        Ganti Metode Pembayaran
                    </button>

                    <a href="{{ route('orders.show', $order->id) }}"
                        class="mt-3 block w-full py-2.5 border border-gold/30 text-warm-white rounded-xl font-medium text-center text-sm hover:bg-dark-tertiary hover:border-gold transition">
                        Kembali ke Detail Pesanan
                    </a>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        function paymentHandler() {
            return {
                loading: false,
                errorMessage: '',
                snapToken: @json($order->payment?->snap_token),
                snapExpired: @json($order->payment?->expired_at?->isPast() ?? false),

                async pay() {
                    this.errorMessage = '';
                    this.loading = true;

                    try {
                        if (this.snapToken && !this.snapExpired) {
                            this.loading = false;
                            this.openSnap(this.snapToken);
                            return;
                        }

                        this.snapToken = null;
                        this.snapExpired = false;

                        const response = await fetch(
                            '{{ route('payment.create', $order->id) }}',
                            {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            }
                        );

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.errorMessage = data.message || 'Gagal membuat transaksi pembayaran.';
                            return;
                        }

                        this.snapToken = data.snap_token;
                        this.openSnap(data.snap_token);

                    } catch (err) {
                        this.errorMessage = 'Tidak dapat terhubung ke server. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },

                async refresh() {
                    this.errorMessage = '';
                    this.loading = true;

                    try {
                        const response = await fetch(
                            '{{ route('payment.refresh', $order->id) }}',
                            {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            }
                        );

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.errorMessage = data.message || 'Gagal memperbarui metode pembayaran.';
                            return;
                        }

                        this.snapToken = data.snap_token;
                        this.snapExpired = false;
                        this.openSnap(data.snap_token);
                    } catch (err) {
                        this.errorMessage = 'Tidak dapat terhubung ke server. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },

                openSnap(token) {
                    snap.pay(token, {
                        onSuccess: (result) => {
                            window.location.href = '{{ route('orders.show', $order->id) }}';
                        },
                        onPending: (result) => {
                            window.location.reload();
                        },
                        onError: (result) => {
                            this.errorMessage = 'Pembayaran gagal. Silakan coba metode pembayaran lain atau hubungi kami.';
                        },
                        onClose: () => {},
                    });
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
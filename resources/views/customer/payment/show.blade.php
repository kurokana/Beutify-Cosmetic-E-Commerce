<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <a href="{{ route('orders.index') }}" class="hover:text-pink-600">Riwayat Pesanan</a>
            <span>/</span>
            <a href="{{ route('orders.show', $order->id) }}" class="hover:text-pink-600">{{ $order->order_number }}</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Pembayaran</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page Title --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Pembayaran</h1>
                <p class="text-sm text-gray-500 mt-0.5">Selesaikan pembayaran untuk pesanan {{ $order->order_number }}</p>
            </div>

            <div class="space-y-5">

                {{-- ── Order Summary ────────────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Ringkasan Pesanan
                    </h2>

                    {{-- Items --}}
                    <div class="divide-y divide-gray-100 mb-4">
                        @foreach ($order->items as $item)
                            <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-gray-500 mt-0.5">Varian: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price breakdown --}}
                    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Produk</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Ongkos Kirim ({{ strtoupper($order->courier_name) }} {{ $order->courier_service }})</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>
                                    Diskon Voucher
                                    @if ($order->voucher)
                                        <span class="text-xs text-gray-400">({{ $order->voucher->code }})</span>
                                    @endif
                                </span>
                                <span class="font-medium">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-100 pt-3 mt-1 flex justify-between font-bold text-gray-900 text-base">
                            <span>Total Pembayaran</span>
                            <span class="text-pink-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- ── Payment Deadline ─────────────────────────────────────── --}}
                @if ($order->payment?->expired_at)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-semibold text-yellow-800">Batas Waktu Pembayaran</p>
                            <p class="text-yellow-700 mt-0.5">
                                {{ $order->payment->expired_at->translatedFormat('d F Y, H:i') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── Payment Methods Info ─────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Metode Pembayaran Tersedia
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-center text-gray-600">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-800 mb-1">Virtual Account</p>
                            <p>BCA, BNI, BRI,<br>Mandiri, Permata</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-800 mb-1">E-Wallet</p>
                            <p>GoPay, OVO,<br>Dana, ShopeePay</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-800 mb-1">Kartu Kredit</p>
                            <p>Visa, Mastercard,<br>JCB, Amex</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-800 mb-1">QRIS</p>
                            <p>Semua aplikasi<br>pembayaran QRIS</p>
                        </div>
                    </div>
                </div>

                {{-- ── Pay Button ───────────────────────────────────────────── --}}
                <div
                    x-data="paymentHandler()"
                    class="bg-white rounded-xl shadow-sm p-6"
                >
                    {{-- Error message --}}
                    <div
                        x-show="errorMessage"
                        x-text="errorMessage"
                        class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"
                    ></div>

                    {{-- Pay Now button --}}
                    <button
                        type="button"
                        @click="pay"
                        :disabled="loading"
                        class="w-full py-4 bg-pink-600 text-white rounded-xl font-bold text-base hover:bg-pink-700 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-3"
                    >
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

                    <p class="text-xs text-center text-gray-400 mt-3">
                        Pembayaran diproses secara aman melalui Midtrans
                    </p>

                    <a
                        href="{{ route('orders.show', $order->id) }}"
                        class="mt-3 block w-full py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium text-center text-sm hover:bg-gray-50 transition"
                    >
                        Kembali ke Detail Pesanan
                    </a>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Midtrans Snap.js — sandbox or production based on config --}}
    <script
        src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"
    ></script>

    <script>
        function paymentHandler() {
            return {
                loading: false,
                errorMessage: '',
                snapToken: @json($order->payment?->snap_token),

                async pay() {
                    this.errorMessage = '';
                    this.loading = true;

                    try {
                        // If we already have a snap_token, use it directly
                        if (this.snapToken) {
                            this.loading = false;
                            this.openSnap(this.snapToken);
                            return;
                        }

                        // Otherwise, request a new snap_token from the server
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

                openSnap(token) {
                    snap.pay(token, {
                        onSuccess: (result) => {
                            // Payment successful — redirect to order detail
                            window.location.href = '{{ route('orders.show', $order->id) }}';
                        },
                        onPending: (result) => {
                            // Payment pending (e.g. VA waiting for transfer) — reload to show updated status
                            window.location.reload();
                        },
                        onError: (result) => {
                            this.errorMessage = 'Pembayaran gagal. Silakan coba metode pembayaran lain atau hubungi kami.';
                        },
                        onClose: () => {
                            // User closed the popup without completing payment — do nothing
                        },
                    });
                },
            };
        }
    </script>
    @endpush
</x-app-layout>

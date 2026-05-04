<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <a href="{{ route('orders.index') }}" class="hover:text-pink-600">Riwayat Pesanan</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $order->order_number }}</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page Title + Status --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->order_number }}</p>
                </div>

                @php
                    $statusConfig = [
                        'pending_payment'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'],
                        'payment_confirmed' => ['label' => 'Pembayaran Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                        'processing'        => ['label' => 'Sedang Diproses', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                        'shipped'           => ['label' => 'Sedang Dikirim', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                        'delivered'         => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800 border-green-200'],
                        'cancelled'         => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800 border-red-200'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800 border-gray-200'];
                @endphp

                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            <div class="space-y-5">

                {{-- ── Order Info ──────────────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Informasi Pesanan
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Nomor Pesanan</dt>
                            <dd class="font-semibold text-gray-900 mt-0.5">{{ $order->order_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tanggal Pesanan</dt>
                            <dd class="font-medium text-gray-900 mt-0.5">
                                {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Status Pesanan</dt>
                            <dd class="mt-0.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </dd>
                        </div>
                        @if ($order->notes)
                            <div>
                                <dt class="text-gray-500">Catatan</dt>
                                <dd class="font-medium text-gray-900 mt-0.5">{{ $order->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- ── Ordered Products ─────────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Produk Dipesan
                    </h2>

                    <div class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <div class="flex items-start gap-4 py-4 first:pt-0 last:pb-0">
                                {{-- Product image placeholder --}}
                                <div class="w-14 h-14 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center overflow-hidden">
                                    @php
                                        $product = $item->product;
                                        $primaryImage = $product?->images?->firstWhere('is_primary', true)
                                            ?? $product?->images?->first();
                                    @endphp
                                    @if ($primaryImage)
                                        <img
                                            src="{{ Storage::url($primaryImage->image_path) }}"
                                            alt="{{ $item->product_name }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 text-sm">{{ $item->product_name }}</p>
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
                </div>

                {{-- ── Shipping Address ─────────────────────────────────────── --}}
                @if ($order->address)
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
                        <div class="text-sm">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900">{{ $order->address->recipient_name }}</span>
                                <span class="text-gray-500">{{ $order->address->phone }}</span>
                                @if ($order->address->label)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        {{ $order->address->label }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600">
                                {{ $order->address->full_address }},
                                {{ $order->address->district }},
                                {{ $order->address->city }},
                                {{ $order->address->province }}
                                {{ $order->address->postal_code }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── Courier Info ─────────────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.5 9h11L19 8" />
                        </svg>
                        Informasi Pengiriman
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Kurir</dt>
                            <dd class="font-medium text-gray-900 mt-0.5">
                                {{ strtoupper($order->courier_name) }} — {{ $order->courier_service }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Ongkos Kirim</dt>
                            <dd class="font-medium text-gray-900 mt-0.5">
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </dd>
                        </div>
                        @if ($order->shipping_tracking_number)
                            <div class="sm:col-span-2">
                                <dt class="text-gray-500">Nomor Resi</dt>
                                <dd class="font-semibold text-gray-900 mt-0.5 font-mono tracking-wide">
                                    {{ $order->shipping_tracking_number }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- ── Payment Info ─────────────────────────────────────────── --}}
                @if ($order->payment)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Status Pembayaran
                        </h2>
                        @php
                            $paymentStatusConfig = [
                                'pending'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'success'   => ['label' => 'Berhasil', 'class' => 'bg-green-100 text-green-800'],
                                'failed'    => ['label' => 'Gagal', 'class' => 'bg-red-100 text-red-800'],
                                'expired'   => ['label' => 'Kedaluwarsa', 'class' => 'bg-gray-100 text-gray-800'],
                                'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                            ];
                            $paymentStatus = $paymentStatusConfig[$order->payment->status] ?? ['label' => $order->payment->status, 'class' => 'bg-gray-100 text-gray-800'];
                        @endphp
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Status</dt>
                                <dd class="mt-0.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatus['class'] }}">
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </dd>
                            </div>
                            @if ($order->payment->payment_method)
                                <div>
                                    <dt class="text-gray-500">Metode Pembayaran</dt>
                                    <dd class="font-medium text-gray-900 mt-0.5 capitalize">
                                        {{ str_replace('_', ' ', $order->payment->payment_method) }}
                                    </dd>
                                </div>
                            @endif
                            @if ($order->payment->paid_at)
                                <div>
                                    <dt class="text-gray-500">Dibayar Pada</dt>
                                    <dd class="font-medium text-gray-900 mt-0.5">
                                        {{ $order->payment->paid_at->translatedFormat('d F Y, H:i') }}
                                    </dd>
                                </div>
                            @endif
                            @if ($order->payment->expired_at && $order->payment->status === 'pending')
                                <div>
                                    <dt class="text-gray-500">Batas Waktu Pembayaran</dt>
                                    <dd class="font-medium text-red-600 mt-0.5">
                                        {{ $order->payment->expired_at->translatedFormat('d F Y, H:i') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- ── Price Summary ────────────────────────────────────────── --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Ringkasan Harga
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Produk</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Ongkos Kirim</span>
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
                        <div class="border-t border-gray-100 pt-3 mt-3 flex justify-between font-bold text-gray-900 text-base">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- ── Action Buttons ───────────────────────────────────────── --}}
                <div class="flex flex-wrap gap-3">

                    {{-- Pay Now — only when pending_payment --}}
                    @if ($order->status === 'pending_payment' && $order->payment?->snap_token)
                        <button
                            type="button"
                            onclick="payNow('{{ $order->payment->snap_token }}')"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Bayar Sekarang
                        </button>
                    @endif

                    {{-- Track Order — only when status is shipped and tracking number exists --}}
                    @if ($order->status === 'shipped' && $order->shipping_tracking_number)
                        <a
                            href="{{ route('orders.track', $order->id) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Lacak Pesanan
                        </a>
                    @endif

                    {{-- Confirm Receipt — only when shipped (for Task 15.2) --}}
                    @if ($order->status === 'shipped')
                        <form method="POST" action="{{ route('orders.confirm', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                onclick="return confirm('Konfirmasi bahwa Anda telah menerima pesanan ini?')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition text-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Penerimaan
                            </button>
                        </form>
                    @endif

                    {{-- Back to orders list --}}
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Riwayat
                    </a>

                </div>

            </div>
        </div>
    </div>

    @if ($order->status === 'pending_payment' && $order->payment?->snap_token)
        @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            function payNow(snapToken) {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.reload();
                    },
                    onPending: function(result) {
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        // User closed the popup without completing payment
                    }
                });
            }
        </script>
        @endpush
    @endif
</x-app-layout>

<x-app-layout>
    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header seperti halaman Katalog --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-border-subtle pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-gold-light">Detail</span>
                        <span class="text-gold">Pesanan</span>
                    </h1>
                    <p class="text-warm-muted text-sm mt-1 font-medium">{{ $order->order_number }}</p>
                </div>
                @php
                    $statusConfig = [
                        'pending_payment'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                        'payment_confirmed' => ['label' => 'Pembayaran Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                        'processing'        => ['label' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                        'shipped'           => ['label' => 'Dikirim', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                        'delivered'         => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800 border-green-200'],
                        'cancelled'         => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800 border-red-200'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['label' => $order->status, 'class' => 'bg-dark-tertiary text-warm-white border-border-subtle'];
                @endphp
                <div class="flex items-center">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wide border {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">

                {{-- Informasi Pesanan --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Informasi Pesanan
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-warm-muted font-medium">Nomor Pesanan</dt>
                            <dd class="font-bold text-warm-white mt-0.5">{{ $order->order_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-warm-muted font-medium">Tanggal Pesanan</dt>
                            <dd class="font-medium text-warm-white mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                        @if ($order->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-warm-muted font-medium">Catatan</dt>
                                <dd class="font-medium text-warm-white mt-0.5">{{ $order->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Produk Dipesan --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Produk Dipesan
                    </h2>
                    <div class="divide-y divide-border-subtle">
                        @foreach ($order->items as $item)
                            <div class="flex items-start gap-4 py-4 first:pt-0 last:pb-0">
                                <div class="w-14 h-14 rounded-xl bg-dark-tertiary border border-border-subtle shrink-0 flex items-center justify-center overflow-hidden">
                                    @php
                                        $product = $item->product;
                                        $primaryImage = $product?->images?->firstWhere('is_primary', true) ?? $product?->images?->first();
                                    @endphp
                                    @if ($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-warm-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-warm-white text-sm">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-warm-muted mt-0.5">Varian: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-warm-muted mt-0.5">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-extrabold text-warm-white shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Alamat Pengiriman --}}
                @if ($order->address)
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Alamat Pengiriman
                    </h2>
                    <div class="text-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-warm-white">{{ $order->address->recipient_name }}</span>
                            <span class="text-warm-muted">{{ $order->address->phone }}</span>
                            @if ($order->address->label)
                                <span class="text-xs bg-dark-tertiary text-warm-gray px-2 py-0.5 rounded-full">{{ $order->address->label }}</span>
                            @endif
                        </div>
                        <p class="text-warm-gray">{{ $order->address->full_address }}, {{ $order->address->district }}, {{ $order->address->city }}, {{ $order->address->province }} {{ $order->address->postal_code }}</p>
                    </div>
                </div>
                @endif

                {{-- Informasi Pengiriman --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.5 9h11L19 8" />
                        </svg>
                        Informasi Pengiriman
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-warm-muted font-medium">Kurir</dt>
                            <dd class="font-bold text-warm-white mt-0.5">{{ strtoupper($order->courier_name) }} — {{ $order->courier_service }}</dd>
                        </div>
                        <div>
                            <dt class="text-warm-muted font-medium">Ongkos Kirim</dt>
                            <dd class="font-bold text-warm-white mt-0.5">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</dd>
                        </div>
                        @if ($order->shipping_tracking_number)
                            <div class="sm:col-span-2">
                                <dt class="text-warm-muted font-medium">Nomor Resi</dt>
                                <dd class="font-mono font-bold text-warm-white mt-0.5">{{ $order->shipping_tracking_number }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Status Pembayaran --}}
                @if ($order->payment)
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Status Pembayaran
                    </h2>
                    @php
                        $paymentStatusConfig = [
                            'pending'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-800'],
                            'success'   => ['label' => 'Berhasil', 'class' => 'bg-green-100 text-green-800'],
                            'failed'    => ['label' => 'Gagal', 'class' => 'bg-red-100 text-red-800'],
                            'expired'   => ['label' => 'Kedaluwarsa', 'class' => 'bg-dark-tertiary text-warm-white'],
                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                        ];
                        $paymentStatus = $paymentStatusConfig[$order->payment->status] ?? ['label' => $order->payment->status, 'class' => 'bg-dark-tertiary text-warm-white'];
                    @endphp
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-warm-muted font-medium">Status</dt>
                            <dd class="mt-0.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span>
                            </dd>
                        </div>
                        @if ($order->payment->payment_method)
                            <div>
                                <dt class="text-warm-muted font-medium">Metode Pembayaran</dt>
                                <dd class="font-bold text-warm-white mt-0.5 capitalize">{{ str_replace('_', ' ', $order->payment->payment_method) }}</dd>
                            </div>
                        @endif
                        @if ($order->payment->paid_at)
                            <div>
                                <dt class="text-warm-muted font-medium">Dibayar Pada</dt>
                                <dd class="font-bold text-warm-white mt-0.5">{{ $order->payment->paid_at->translatedFormat('d F Y, H:i') }}</dd>
                            </div>
                        @endif
                        @if ($order->payment->expired_at && $order->payment->status === 'pending')
                            <div>
                                <dt class="text-warm-muted font-medium">Batas Waktu</dt>
                                <dd class="font-bold text-red-600 mt-0.5">{{ $order->payment->expired_at->translatedFormat('d F Y, H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                @endif

                {{-- Ringkasan Harga --}}
                <div class="bg-dark-secondary rounded-2xl border border-border-subtle shadow-sm p-6">
                    <h2 class="text-base font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Ringkasan Harga
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-warm-gray">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-warm-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-warm-gray">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold text-warm-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon Voucher @if($order->voucher)<span class="text-xs text-warm-muted">({{ $order->voucher->code }})</span>@endif</span>
                                <span class="font-bold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gold/30/30 pt-3 mt-3 flex justify-between font-black text-warm-white text-base">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                @php
                    $refundDeadline = $order->delivered_at ? $order->delivered_at->copy()->addDay() : null;
                    $refundExpired = $refundDeadline ? now()->gt($refundDeadline) : false;
                    $canRequestRefund = $order->status === 'delivered' && ! $order->refund_requested_at && ! $refundExpired;
                @endphp
                <div class="flex flex-wrap gap-3">
                    @if ($order->status === 'pending_payment' && $order->payment?->snap_token)
                        <button type="button" onclick="payNow('{{ $order->payment->snap_token }}')"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gold text-white rounded-xl font-bold hover:bg-[#d45a92] transition shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Bayar Sekarang
                        </button>
                    @endif

                    @if ($order->status === 'shipped' && $order->shipping_tracking_number)
                        <a href="{{ route('orders.track', $order->id) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Lacak Pesanan
                        </a>
                    @endif

                    @if ($order->status === 'shipped')
                        <form method="POST" action="{{ route('orders.confirm', $order->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" onclick="return confirm('Konfirmasi bahwa Anda telah menerima pesanan ini?')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Penerimaan
                            </button>
                        </form>
                    @endif

                    @if ($canRequestRefund)
                        <form method="POST" action="{{ route('orders.refund', $order->id) }}" class="w-full">
                            @csrf
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <p class="text-sm text-red-700 font-semibold mb-2">Ajukan refund maksimal 1x24 jam setelah pesanan selesai.</p>
                                <textarea name="refund_reason" rows="2" placeholder="Alasan refund (opsional)"
                                    class="w-full px-3 py-2 text-sm border border-red-200 rounded-lg focus:ring-2 focus:ring-red-300 focus:border-transparent"></textarea>
                                <button type="submit" onclick="return confirm('Ajukan refund untuk pesanan ini?')"
                                    class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414 1.414M5.636 5.636l1.414 1.414M17.657 17.657l1.414 1.414M12 8v4l3 3" />
                                    </svg>
                                    Ajukan Refund
                                </button>
                            </div>
                        </form>
                    @elseif ($order->status === 'delivered' && $order->refund_requested_at)
                        <div class="w-full bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700 font-semibold">
                            Refund sudah diajukan pada {{ $order->refund_requested_at->translatedFormat('d F Y, H:i') }}.
                        </div>
                    @elseif ($order->status === 'delivered' && ! $order->delivered_at)
                        <div class="w-full bg-dark-tertiary border border-border-subtle rounded-xl p-4 text-sm text-warm-gray font-semibold">
                            Waktu penyelesaian pesanan belum tercatat. Silakan hubungi admin jika tombol refund tidak muncul.
                        </div>
                    @elseif ($order->status === 'delivered' && $refundExpired)
                        <div class="w-full bg-dark-tertiary border border-border-subtle rounded-xl p-4 text-sm text-warm-gray font-semibold">
                            Masa pengajuan refund (1x24 jam) sudah berakhir.
                        </div>
                    @endif

                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 border border-gold/30 text-warm-white rounded-xl font-medium hover:bg-dark-tertiary hover:border-gold transition">
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
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            function payNow(snapToken) {
                snap.pay(snapToken, {
                    onSuccess: function(result) { window.location.reload(); },
                    onPending: function(result) { window.location.reload(); },
                    onError: function(result) { alert('Pembayaran gagal. Silakan coba lagi.'); },
                    onClose: function() {}
                });
            }
        </script>
        @endpush
    @endif
</x-app-layout>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Dikonfirmasi - Pesanan #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0; color: #374151; }
        .container { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .header { background: #16a34a; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #dcfce7; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .section-title { font-size: 15px; font-weight: 700; color: #111827; margin: 24px 0 12px; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px; }
        .info-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #111827; text-align: right; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 8px 0; color: #6b7280; font-weight: 600; border-bottom: 1px solid #f3f4f6; }
        td { padding: 10px 0; border-bottom: 1px solid #f9fafb; vertical-align: top; }
        .total-row td { font-weight: 700; font-size: 15px; border-bottom: none; padding-top: 16px; }
        .badge { display: inline-block; background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 24px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; background: #16a34a; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 16px; }
        .success-icon { font-size: 48px; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="success-icon">✓</div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Pesanan Anda sedang diproses.</p>
    </div>

    <div class="body">
        <p style="font-size:14px;">Halo <strong>{{ $order->user->name }}</strong>,</p>
        <p style="font-size:14px;">Pembayaran Anda telah berhasil dikonfirmasi. Pesanan Anda akan segera diproses dan dikirimkan ke alamat tujuan.</p>

        {{-- Order Info --}}
        <div class="section-title">Informasi Pesanan</div>
        <div class="info-row">
            <span class="info-label">Nomor Pesanan</span>
            <span class="info-value">{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Pesanan</span>
            <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
        </div>
        @if ($order->payment && $order->payment->paid_at)
        <div class="info-row">
            <span class="info-label">Tanggal Pembayaran</span>
            <span class="info-value">{{ $order->payment->paid_at->format('d M Y, H:i') }} WIB</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value"><span class="badge">Pembayaran Dikonfirmasi</span></span>
        </div>
        @if ($order->payment && $order->payment->payment_type)
        <div class="info-row">
            <span class="info-label">Metode Pembayaran</span>
            <span class="info-value">{{ strtoupper(str_replace('_', ' ', $order->payment->payment_type)) }}</span>
        </div>
        @endif

        {{-- Shipping Address --}}
        @if ($order->address)
        <div class="section-title">Alamat Pengiriman</div>
        <p style="font-size:14px; margin:0;">
            <strong>{{ $order->address->recipient_name }}</strong> — {{ $order->address->phone }}<br>
            {{ $order->address->full_address }},<br>
            {{ $order->address->district }}, {{ $order->address->city }},<br>
            {{ $order->address->province }} {{ $order->address->postal_code }}
        </p>
        @endif

        {{-- Courier --}}
        <div class="section-title">Pengiriman</div>
        <div class="info-row">
            <span class="info-label">Kurir</span>
            <span class="info-value">{{ strtoupper($order->courier_name) }} — {{ $order->courier_service }}</span>
        </div>
        <p style="font-size:13px; color:#6b7280; margin-top:8px;">
            Pesanan Anda akan segera diproses dan dikirimkan. Anda akan menerima email notifikasi dengan nomor resi pengiriman setelah paket dikirim.
        </p>

        {{-- Order Items --}}
        <div class="section-title">Produk yang Dipesan</div>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if ($item->variant_name)
                            <br><span style="color:#6b7280;font-size:12px;">{{ $item->variant_name }}</span>
                        @endif
                        <br><span style="color:#6b7280;font-size:12px;">Rp {{ number_format($item->price, 0, ',', '.') }} / pcs</span>
                    </td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Price Summary --}}
        <div class="section-title">Ringkasan Harga</div>
        <div class="info-row">
            <span class="info-label">Subtotal Produk</span>
            <span class="info-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ongkos Kirim</span>
            <span class="info-value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
        </div>
        @if ($order->discount_amount > 0)
        <div class="info-row">
            <span class="info-label">Diskon Voucher</span>
            <span class="info-value" style="color:#16a34a;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="info-row" style="border-top:2px solid #f3f4f6; padding-top:12px; margin-top:8px;">
            <span style="font-weight:700; font-size:15px;">Total Dibayar</span>
            <span style="font-weight:700; font-size:15px; color:#16a34a;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>

        {{-- Next Steps --}}
        <div class="section-title">Langkah Selanjutnya</div>
        <p style="font-size:14px; color:#374151;">
            Pesanan Anda sedang diproses oleh tim kami. Anda dapat melacak status pesanan dan pengiriman melalui halaman detail pesanan.
        </p>

        <div style="text-align:center;">
            <a href="{{ url('/orders/' . $order->id) }}" class="btn">Lihat Detail Pesanan</a>
        </div>

        <p style="font-size:12px; color:#9ca3af; margin-top:24px;">
            Jika Anda memiliki pertanyaan tentang pesanan ini, silakan hubungi layanan pelanggan kami.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
    </div>
</div>
</body>
</html>

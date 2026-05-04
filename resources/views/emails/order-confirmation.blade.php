<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0; color: #374151; }
        .container { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .header { background: #db2777; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #fce7f3; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .section-title { font-size: 15px; font-weight: 700; color: #111827; margin: 24px 0 12px; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px; }
        .info-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; }
        .info-label { color: #6b7280; }
        .info-value { font-weight: 600; color: #111827; text-align: right; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 8px 0; color: #6b7280; font-weight: 600; border-bottom: 1px solid #f3f4f6; }
        td { padding: 10px 0; border-bottom: 1px solid #f9fafb; vertical-align: top; }
        .total-row td { font-weight: 700; font-size: 15px; border-bottom: none; padding-top: 16px; }
        .badge { display: inline-block; background: #fce7f3; color: #db2777; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 24px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; background: #db2777; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 16px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Pesanan Dikonfirmasi!</h1>
        <p>Terima kasih telah berbelanja di toko kami.</p>
    </div>

    <div class="body">
        <p style="font-size:14px;">Halo <strong>{{ $order->user->name }}</strong>,</p>
        <p style="font-size:14px;">Pesanan Anda telah berhasil dibuat. Silakan selesaikan pembayaran sebelum batas waktu yang ditentukan.</p>

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
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value"><span class="badge">Menunggu Pembayaran</span></span>
        </div>
        @if ($order->payment)
        <div class="info-row">
            <span class="info-label">Batas Pembayaran</span>
            <span class="info-value">{{ $order->payment->expired_at?->format('d M Y, H:i') }} WIB</span>
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
            <span style="font-weight:700; font-size:15px;">Total Pembayaran</span>
            <span style="font-weight:700; font-size:15px; color:#db2777;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>

        {{-- Payment Instructions --}}
        <div class="section-title">Instruksi Pembayaran</div>
        <p style="font-size:14px; color:#374151;">
            Silakan selesaikan pembayaran sebesar <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            melalui halaman pembayaran di bawah ini. Pembayaran mendukung berbagai metode termasuk virtual account,
            e-wallet (GoPay, OVO, Dana), kartu kredit, dan QRIS.
        </p>

        <div style="text-align:center;">
            <a href="{{ url('/orders/' . $order->id) }}" class="btn">Bayar Sekarang</a>
        </div>

        <p style="font-size:12px; color:#9ca3af; margin-top:24px;">
            Jika Anda tidak merasa melakukan pemesanan ini, abaikan email ini atau hubungi layanan pelanggan kami.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
    </div>
</div>
</body>
</html>

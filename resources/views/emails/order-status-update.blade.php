<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ec4899;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-old {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-new {
            background-color: #d1fae5;
            color: #065f46;
        }
        .order-info {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-info h3 {
            margin-top: 0;
            color: #1f2937;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
        }
        .info-value {
            font-weight: bold;
            color: #1f2937;
        }
        .button {
            display: inline-block;
            background-color: #ec4899;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Update Status Pesanan</h1>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $order->user->name }}</strong>,</p>

        <p>Status pesanan Anda telah diperbarui:</p>

        <div style="text-align: center; margin: 20px 0;">
            <div class="status-badge status-old">{{ $oldStatus }}</div>
            <div style="margin: 10px 0;">↓</div>
            <div class="status-badge status-new">{{ $newStatus }}</div>
        </div>

        <div class="order-info">
            <h3>Detail Pesanan</h3>
            <div class="info-row">
                <span class="info-label">Nomor Pesanan:</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pesanan:</span>
                <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total:</span>
                <span class="info-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            @if ($order->shipping_tracking_number)
                <div class="info-row">
                    <span class="info-label">Nomor Resi:</span>
                    <span class="info-value">{{ $order->shipping_tracking_number }}</span>
                </div>
            @endif
        </div>

        @if ($newStatus === 'Sedang Dikirim')
            <p>Pesanan Anda sedang dalam perjalanan! Anda dapat melacak pengiriman menggunakan nomor resi di atas.</p>
        @elseif ($newStatus === 'Selesai')
            <p>Terima kasih telah berbelanja dengan kami! Kami harap Anda puas dengan produk yang diterima.</p>
        @elseif ($newStatus === 'Dibatalkan')
            <p>Pesanan Anda telah dibatalkan. Jika Anda sudah melakukan pembayaran, dana akan dikembalikan dalam 3-7 hari kerja.</p>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('orders.show', $order) }}" class="button">
                Lihat Detail Pesanan
            </a>
        </div>

        <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.</p>

        <p>Terima kasih,<br>
        <strong>Tim E-Commerce Kosmetik</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} E-Commerce Kosmetik. All rights reserved.</p>
    </div>
</body>
</html>

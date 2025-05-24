<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $pesanan->nomor_pesanan }}</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 120px;
        }
        .company-details {
            text-align: right;
        }
        .company-details h2 {
            margin: 0;
            font-size: 18px;
        }
        .company-contact {
            font-size: 11px;
            line-height: 1.4;
            margin-top: 5px;
            color: #555;
        }
        .invoice-info {
            margin-top: 10px;
            line-height: 1.4;
        }
        .section-title {
            background-color: #f5f5f5;
            padding: 8px 12px;
            margin: 20px 0 10px;
            font-weight: bold;
            border-left: 4px solid #1e3a8a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #fafafa;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table td {
            border-bottom: 1px solid #eee;
            padding: 10px;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-top: 10px;
        }
        .totals td {
            padding: 6px 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('front/assets/logo.png') }}" alt="Apotiku Logo">
        </div>
        <div class="company-details">
            <h2>APOTIKU</h2>
            <div class="company-contact">
                Jl. Contoh Alamat No.123, Kel. Contoh, Kec. Contoh, Kota Contoh, Provinsi Contoh, 12345<br>
                Telp: (021) 12345678 • Email: info@apotiku.co.id • Website: www.apotiku.co.id
            </div>
            <div class="invoice-info">
                <div>Invoice #: <strong>{{ $pesanan->nomor_pesanan }}</strong></div>
                <div>Tanggal : {{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d-m-Y') }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Data Pemesan</div>
    <p>
        <strong>{{ $pesanan->profile->nama_lengkap }}</strong><br>
        {{ $pesanan->profile->user->email }}
    </p>

    <div class="section-title">Data Pengiriman</div>
    <p>
        <strong>Alamat:</strong> {{ $pesanan->pengiriman->alamat ?? '-' }}<br>
        <strong>Jarak:</strong> {{ $pesanan->pengiriman->jarak ?? 0 }} km<br>
        <strong>Ongkir:</strong> Rp{{ number_format($pesanan->pengiriman->total ?? 0,0,',','.') }}
    </p>

    <div class="section-title">Detail Produk</div>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->items as $item)
                <tr>
                    <td>{{ $item->obat->nama_obat }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">Rp{{ number_format($item->obat->harga,0,',','.') }}</td>
                    <td class="text-right">Rp{{ number_format($item->total,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td><strong>Subtotal Produk</strong></td>
            <td class="text-right">Rp{{ number_format($pesanan->items->sum('total'),0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Ongkir</strong></td>
            <td class="text-right">Rp{{ number_format($pesanan->pengiriman->total ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Pembayaran</strong></td>
            <td class="text-right"><strong>Rp{{ number_format($pesanan->total,0,',','.') }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di Apotiku!<br>
        Jl. Contoh Alamat No.123, Kel. Contoh, Kec. Contoh, Kota Contoh, Provinsi Contoh, 12345 • Telp: (021) 12345678
    </div>
</body>
</html>

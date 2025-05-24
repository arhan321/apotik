@component('mail::message')
# Invoice Pesanan #{{ $pesanan->nomor_pesanan }}

Halo **{{ $pesanan->profile->nama_lengkap }}**,  
Terima kasih telah melakukan pembayaran di Apotiku. Berikut detail pesanan Anda:

@component('mail::table')
| Produk | Qty | Harga Satuan | Subtotal |
|:-------|:--:|------------:|---------:|
@foreach($pesanan->items as $item)
| {{ $item->obat->nama_obat }} | {{ $item->qty }} | Rp{{ number_format($item->obat->harga,0,',','.') }} | Rp{{ number_format($item->total,0,',','.') }} |
@endforeach
| **Subtotal Produk** |  |  | **Rp{{ number_format($pesanan->items->sum('total'),0,',','.') }}** |
| **Ongkir**         |  |  | **Rp{{ number_format($pesanan->pengiriman->total ?? 0,0,',','.') }}** |
| **Total**          |  |  | **Rp{{ number_format($pesanan->total,0,',','.') }}** |
@endcomponent

@component('mail::button', ['url' => route('pesanan.invoice', $pesanan->id)])
Download PDF Invoice
@endcomponent

Jika ada pertanyaan, balas email ini atau hubungi tim customer service kami.  

Terima kasih telah berbelanja di Apotiku!  
**Apotiku Team**
@endcomponent
@extends('layouts.index')
@section('content')

<section class="container py-5">
  <h2 class="fw-bold mb-4 text-center">Daftar Pesanan Anda</h2>

  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

      @forelse ($pesanans as $pesanan)
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <div>
                <strong>{{ $pesanan->pengajuan_id ? 'ID Pengajuan:' : 'ID Pesanan:' }}</strong>
                #{{ $pesanan->nomor_pesanan }}
              </div>
              <div class="mb-1">
                <span class="badge {{ $pesanan->pengajuan_id ? 'bg-primary' : 'bg-success' }}">
                  {{ $pesanan->pengajuan_id ? 'Pesanan dengan Resep Dokter' : 'Pesanan Tanpa Resep Dokter' }}
                </span>
              </div>
              <div>
                <strong>Status:</strong>
                <span class="badge
                  {{ $pesanan->status === 'menunggu'  ? 'bg-warning text-dark'  : '' }}
                  {{ $pesanan->status === 'diproses'  ? 'bg-info text-white'    : '' }}
                  {{ $pesanan->status === 'dikirim'   ? 'bg-primary text-white' : '' }}
                  {{ $pesanan->status === 'selesai'   ? 'bg-success text-white' : '' }}
                  {{ $pesanan->status === 'dibatalkan'? 'bg-danger text-white'  : '' }}">
                  {{ ucfirst($pesanan->status) }}
                </span>
              </div>
              <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pesanan->tanggal)->translatedFormat('d F Y') }}</div>
              <div><strong>Total:</strong> Rp{{ number_format($pesanan->total, 0, ',', '.') }}</div>
            </div>
            <button class="btn btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#pesananModal{{ $pesanan->id }}">
              Lihat Detail
            </button>
          </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade"
             id="pesananModal{{ $pesanan->id }}"
             tabindex="-1"
             aria-labelledby="modalLabel{{ $pesanan->id }}"
             aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel{{ $pesanan->id }}">
                  Detail {{ $pesanan->pengajuan_id ? 'Pengajuan' : 'Pesanan' }}
                  #{{ $pesanan->nomor_pesanan }}
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
              </div>
              <div class="modal-body p-4">
                {{-- Data Pemesan --}}
                <div class="mb-3">
                  <h6 class="fw-bold">Data Pemesan</h6>
                  <p class="mb-0">
                    {{ $pesanan->profile->nama_lengkap ?? '-' }}<br>
                    <small>{{ $pesanan->profile->user->email ?? '-' }}</small>
                  </p>
                </div>

                {{-- Jenis --}}
                <p>
                  <span class="badge {{ $pesanan->pengajuan_id ? 'bg-primary' : 'bg-success' }}">
                    {{ $pesanan->pengajuan_id ? 'Pesanan dengan Resep Dokter' : 'Pesanan Tanpa Resep Dokter' }}
                  </span>
                </p>

                {{-- Status --}}
                <div class="mb-3">
                  <h6 class="fw-bold">Status Pesanan</h6>
                  <span class="badge
                    {{ $pesanan->status === 'menunggu'   ? 'bg-warning text-dark'  : '' }}
                    {{ $pesanan->status === 'diproses'   ? 'bg-info text-white'    : '' }}
                    {{ $pesanan->status === 'dikirim'    ? 'bg-primary text-white' : '' }}
                    {{ $pesanan->status === 'selesai'    ? 'bg-success text-white' : '' }}
                    {{ $pesanan->status === 'dibatalkan' ? 'bg-danger text-white'  : '' }}">
                    {{ ucfirst($pesanan->status) }}
                  </span>
                </div>

                {{-- Produk --}}
                <h6 class="fw-bold mt-4">Produk Dipesan</h6>
                @if ($pesanan->items->count())
                  <ul class="list-group mb-3">
                    @foreach ($pesanan->items as $item)
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item->obat->nama_obat ?? '(Obat tidak ditemukan)' }}
                        x{{ $item->qty }}
                        <span>Rp{{ number_format($item->total, 0, ',', '.') }}</span>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-muted">Belum ada produk dalam pesanan ini.</p>
                @endif

                {{-- Ringkasan biaya --}}
                <div class="mb-2 d-flex justify-content-between">
                  <strong>Subtotal Produk:</strong>
                  <span>Rp{{ number_format($pesanan->items->sum('total'), 0, ',', '.') }}</span>
                </div>
                <div class="mb-3">
                  <h6 class="fw-bold">Alamat Pengiriman</h6>
                  <p class="mb-0">
                    {{ $pesanan->pengiriman->alamat ?? '-' }}
                  </p>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                  <strong>Ongkir:</strong>
                  <span>Rp{{ number_format($pesanan->pengiriman->total ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="border-top pt-3 d-flex justify-content-between fw-bold">
                  <span>Total Pembayaran:</span>
                  <span>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</span>
                </div>

                {{-- Tombol Bayar Sekarang --}}
                @if($pesanan->pengajuan_id && $pesanan->status === 'menunggu')
                  <div class="mt-3 text-end">
                    <button
                      class="btn btn-success btn-pay-now"
                      data-pesanan-id="{{ $pesanan->id }}">
                      Bayar Sekarang
                    </button>
                  </div>
                @endif
                <div class="modal-footer">
                  <a href="{{ route('pesanan.invoice', $pesanan->id) }}"
                    class="btn btn-outline-secondary"
                    target="_blank">
                    Download Invoice
                  </a>
                  <button type="button"
                          class="btn btn-primary"
                          data-bs-dismiss="modal">
                    Tutup
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-info text-center">Belum ada pesanan.</div>
      @endforelse

    </div>
  </div>
</section>

@endsection

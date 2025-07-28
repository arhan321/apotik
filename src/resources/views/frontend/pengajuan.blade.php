@extends('layouts.index')
@section('content')

<section class="container py-5">
  <h2 class="fw-bold mb-4 text-center">Daftar Pengajuan Obat</h2>

  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

      @forelse($pengajuans as $pengajuan)
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <div><strong>ID Pengajuan:</strong> {{ $pengajuan->nomor_pengajuan }}</div>
              <div>
                <strong>Status:</strong>
                @if($pengajuan->status === 'menunggu')
                  <span class="badge bg-warning text-white">Menunggu Konfirmasi</span>
                @elseif($pengajuan->status === 'disetujui')
                  <span class="badge bg-success text-white">Sudah Dikonfirmasi</span>
                @elseif($pengajuan->status === 'ditolak')
                  <span class="badge bg-danger text-white">Ditolak</span>
                @else
                  <span class="badge bg-info text-white">Diproses</span>
                @endif
              </div>
              <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal)->translatedFormat('d F Y') }}</div>
            </div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPengajuan{{ $pengajuan->id }}">Lihat Detail</button>
          </div>
        </div>

        <!-- Modal Detail Pengajuan -->
        <div class="modal fade" id="modalPengajuan{{ $pengajuan->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $pengajuan->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel{{ $pengajuan->id }}">Detail Pengajuan {{ $pengajuan->nomor_pengajuan }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">

                <!-- Info Pemesan -->
                <div class="mb-3">
                  <span class="badge bg-primary px-3 py-2 rounded-pill">Pesanan dengan Resep Dokter</span>
                </div>

                <div class="mb-1 fw-semibold">Status Pesanan:</div>
                <div class="mb-3">
                  <span class="badge bg-{{ $pengajuan->status === 'menunggu' ? 'warning' : ($pengajuan->status === 'disetujui' ? 'success' : ($pengajuan->status === 'ditolak' ? 'danger' : 'info')) }} px-3 py-2 rounded-pill text-white">
                    {{ ucfirst($pengajuan->status) }}
                  </span>
                </div>

                <div class="alert alert-info small py-2 px-3 mb-3" role="alert">
                  {{ $pengajuan->status === 'menunggu' ? '⏳ Tunggu ya, pengajuanmu sedang diproses.' : ($pengajuan->status === 'ditolak' ? '❌ Maaf, pengajuanmu ditolak.' : '✅ Pengajuanmu sudah dikonfirmasi.') }}
                </div>

                <!-- Foto Resep -->
                <div class="mb-3">
                  <label class="fw-semibold">Foto Resep:</label>
                  <div class="border rounded p-2 text-center">
                    <img src="{{ asset('storage/' . $pengajuan->image) }}" alt="Resep Dokter" class="img-fluid rounded" style="max-height: 400px;">
                  </div>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                  <label class="fw-semibold">Catatan:</label>
                  <div class="bg-light border rounded p-3">
                    {{ $pengajuan->catatan ?? '-' }}
                  </div>
                </div>

                <!-- Keterangan (jika ditolak) -->
                @if($pengajuan->status === 'ditolak')
                  <div class="mb-3">
                    <label class="fw-semibold">Keterangan:</label>
                    <div class="bg-light border rounded p-3">
                      {{ $pengajuan->keterangan ?? '-' }}
                    </div>
                  </div>
                @endif

                <!-- Estimasi Jarak dan Ongkir -->
                <div class="mb-3 d-flex justify-content-between">
                  <span class="fw-semibold">Jarak ke Apotek:</span>
                  <span class="text-muted">{{ $pengajuan->jarak }} km</span>
                </div>
                <div class="mb-3 d-flex justify-content-between">
                  <span class="fw-semibold">Ongkir Estimasi:</span>
                  <span class="fw-bold text-success">Rp{{ number_format($pengajuan->total, 0, ',', '.') }}</span>
                </div>

              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-warning">Belum ada pengajuan resep.</div>
      @endforelse

    </div>
  </div>
</section>

@endsection

<?php $__env->startSection('content'); ?>

<section class="container py-5">
  <h2 class="fw-bold mb-4 text-center">Daftar Pengajuan Obat</h2>

  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

      <?php $__empty_1 = true; $__currentLoopData = $pengajuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengajuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <div><strong>ID Pengajuan:</strong> <?php echo e($pengajuan->nomor_pengajuan); ?></div>
              <div>
                <strong>Status:</strong>
                <?php if($pengajuan->status === 'menunggu'): ?>
                  <span class="badge bg-warning text-white">Menunggu Konfirmasi</span>
                <?php elseif($pengajuan->status === 'disetujui'): ?>
                  <span class="badge bg-success text-white">Sudah Dikonfirmasi</span>
                <?php elseif($pengajuan->status === 'ditolak'): ?>
                  <span class="badge bg-danger text-white">Ditolak</span>
                <?php else: ?>
                  <span class="badge bg-info text-white">Diproses</span>
                <?php endif; ?>
              </div>
              <div><strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($pengajuan->tanggal)->translatedFormat('d F Y')); ?></div>
            </div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPengajuan<?php echo e($pengajuan->id); ?>">Lihat Detail</button>
          </div>
        </div>

        <!-- Modal Detail Pengajuan -->
        <div class="modal fade" id="modalPengajuan<?php echo e($pengajuan->id); ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo e($pengajuan->id); ?>" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel<?php echo e($pengajuan->id); ?>">Detail Pengajuan <?php echo e($pengajuan->nomor_pengajuan); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">

                <!-- Info Pemesan -->
                <div class="mb-3">
                  <span class="badge bg-primary px-3 py-2 rounded-pill">Pesanan dengan Resep Dokter</span>
                </div>

                <div class="mb-1 fw-semibold">Status Pesanan:</div>
                <div class="mb-3">
                  <span class="badge bg-<?php echo e($pengajuan->status === 'menunggu' ? 'warning' : ($pengajuan->status === 'disetujui' ? 'success' : ($pengajuan->status === 'ditolak' ? 'danger' : 'info'))); ?> px-3 py-2 rounded-pill text-white">
                    <?php echo e(ucfirst($pengajuan->status)); ?>

                  </span>
                </div>

                <div class="alert alert-info small py-2 px-3 mb-3" role="alert">
                  <?php echo e($pengajuan->status === 'menunggu' ? '⏳ Tunggu ya, pengajuanmu sedang diproses.' : ($pengajuan->status === 'ditolak' ? '❌ Maaf, pengajuanmu ditolak.' : '✅ Pengajuanmu sudah dikonfirmasi.')); ?>

                </div>

                <!-- Foto Resep -->
                <div class="mb-3">
                  <label class="fw-semibold">Foto Resep:</label>
                  <div class="border rounded p-2 text-center">
                    <img src="<?php echo e(asset('storage/' . $pengajuan->image)); ?>" alt="Resep Dokter" class="img-fluid rounded" style="max-height: 400px;">
                  </div>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                  <label class="fw-semibold">Catatan:</label>
                  <div class="bg-light border rounded p-3">
                    <?php echo e($pengajuan->catatan ?? '-'); ?>

                  </div>
                </div>

                <!-- Keterangan (jika ditolak) -->
                <?php if($pengajuan->status === 'ditolak'): ?>
                  <div class="mb-3">
                    <label class="fw-semibold">Keterangan:</label>
                    <div class="bg-light border rounded p-3">
                      <?php echo e($pengajuan->keterangan ?? '-'); ?>

                    </div>
                  </div>
                <?php endif; ?>

                <!-- Estimasi Jarak dan Ongkir -->
                <div class="mb-3 d-flex justify-content-between">
                  <span class="fw-semibold">Jarak ke Apotek:</span>
                  <span class="text-muted"><?php echo e($pengajuan->jarak); ?> km</span>
                </div>
                <div class="mb-3 d-flex justify-content-between">
                  <span class="fw-semibold">Ongkir Estimasi:</span>
                  <span class="fw-bold text-success">Rp<?php echo e(number_format($pengajuan->total, 0, ',', '.')); ?></span>
                </div>

              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="alert alert-warning">Belum ada pengajuan resep.</div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/frontend/pengajuan.blade.php ENDPATH**/ ?>
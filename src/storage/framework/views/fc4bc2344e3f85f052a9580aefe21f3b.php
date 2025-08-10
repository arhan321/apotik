<?php $__env->startSection('content'); ?>

<section class="container py-5">
  <h2 class="fw-bold mb-4 text-center">Daftar Pesanan Anda</h2>

  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

      <?php $__empty_1 = true; $__currentLoopData = $pesanans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pesanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <div>
                <strong><?php echo e($pesanan->pengajuan_id ? 'ID Pengajuan:' : 'ID Pesanan:'); ?></strong>
                #<?php echo e($pesanan->nomor_pesanan); ?>

              </div>
              <div class="mb-1">
                <span class="badge <?php echo e($pesanan->pengajuan_id ? 'bg-primary' : 'bg-success'); ?>">
                  <?php echo e($pesanan->pengajuan_id ? 'Pesanan dengan Resep Dokter' : 'Pesanan Tanpa Resep Dokter'); ?>

                </span>
              </div>
              <div>
                <strong>Status:</strong>
                <span class="badge
                  <?php echo e($pesanan->status === 'menunggu'  ? 'bg-warning text-dark'  : ''); ?>

                  <?php echo e($pesanan->status === 'diproses'  ? 'bg-info text-white'    : ''); ?>

                  <?php echo e($pesanan->status === 'dikirim'   ? 'bg-primary text-white' : ''); ?>

                  <?php echo e($pesanan->status === 'selesai'   ? 'bg-success text-white' : ''); ?>

                  <?php echo e($pesanan->status === 'dibatalkan'? 'bg-danger text-white'  : ''); ?>">
                  <?php echo e(ucfirst($pesanan->status)); ?>

                </span>
              </div>
              <div><strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($pesanan->tanggal)->translatedFormat('d F Y')); ?></div>
              <div><strong>Total:</strong> Rp<?php echo e(number_format($pesanan->total, 0, ',', '.')); ?></div>
            </div>
            <button class="btn btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#pesananModal<?php echo e($pesanan->id); ?>">
              Lihat Detail
            </button>
          </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade"
             id="pesananModal<?php echo e($pesanan->id); ?>"
             tabindex="-1"
             aria-labelledby="modalLabel<?php echo e($pesanan->id); ?>"
             aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel<?php echo e($pesanan->id); ?>">
                  Detail <?php echo e($pesanan->pengajuan_id ? 'Pengajuan' : 'Pesanan'); ?>

                  #<?php echo e($pesanan->nomor_pesanan); ?>

                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
              </div>
              <div class="modal-body p-4">
                
                <div class="mb-3">
                  <h6 class="fw-bold">Data Pemesan</h6>
                  <p class="mb-0">
                    <?php echo e($pesanan->profile->nama_lengkap ?? '-'); ?><br>
                    <small><?php echo e($pesanan->profile->user->email ?? '-'); ?></small>
                  </p>
                </div>

                
                <p>
                  <span class="badge <?php echo e($pesanan->pengajuan_id ? 'bg-primary' : 'bg-success'); ?>">
                    <?php echo e($pesanan->pengajuan_id ? 'Pesanan dengan Resep Dokter' : 'Pesanan Tanpa Resep Dokter'); ?>

                  </span>
                </p>

                
                <div class="mb-3">
                  <h6 class="fw-bold">Status Pesanan</h6>
                  <span class="badge
                    <?php echo e($pesanan->status === 'menunggu'   ? 'bg-warning text-dark'  : ''); ?>

                    <?php echo e($pesanan->status === 'diproses'   ? 'bg-info text-white'    : ''); ?>

                    <?php echo e($pesanan->status === 'dikirim'    ? 'bg-primary text-white' : ''); ?>

                    <?php echo e($pesanan->status === 'selesai'    ? 'bg-success text-white' : ''); ?>

                    <?php echo e($pesanan->status === 'dibatalkan' ? 'bg-danger text-white'  : ''); ?>">
                    <?php echo e(ucfirst($pesanan->status)); ?>

                  </span>
                </div>

                
                <h6 class="fw-bold mt-4">Produk Dipesan</h6>
                <?php if($pesanan->items->count()): ?>
                  <ul class="list-group mb-3">
                    <?php $__currentLoopData = $pesanan->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo e($item->obat->nama_obat ?? '(Obat tidak ditemukan)'); ?>

                        x<?php echo e($item->qty); ?>

                        <span>Rp<?php echo e(number_format($item->total, 0, ',', '.')); ?></span>
                      </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                <?php else: ?>
                  <p class="text-muted">Belum ada produk dalam pesanan ini.</p>
                <?php endif; ?>

                
                <div class="mb-2 d-flex justify-content-between">
                  <strong>Subtotal Produk:</strong>
                  <span>Rp<?php echo e(number_format($pesanan->items->sum('total'), 0, ',', '.')); ?></span>
                </div>
                <div class="mb-3">
                  <h6 class="fw-bold">Alamat Pengiriman</h6>
                  <p class="mb-0">
                    <?php echo e($pesanan->pengiriman->alamat ?? '-'); ?>

                  </p>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                  <strong>Ongkir:</strong>
                  <span>Rp<?php echo e(number_format($pesanan->pengiriman->total ?? 0, 0, ',', '.')); ?></span>
                </div>
                <div class="border-top pt-3 d-flex justify-content-between fw-bold">
                  <span>Total Pembayaran:</span>
                  <span>Rp<?php echo e(number_format($pesanan->total, 0, ',', '.')); ?></span>
                </div>

                
                <?php if($pesanan->pengajuan_id && $pesanan->status === 'menunggu'): ?>
                  <div class="mt-3 text-end">
                    <button
                      class="btn btn-success btn-pay-now"
                      data-pesanan-id="<?php echo e($pesanan->id); ?>">
                      Bayar Sekarang
                    </button>
                  </div>
                <?php endif; ?>
                <div class="modal-footer">
                  <a href="<?php echo e(route('pesanan.invoice', $pesanan->id)); ?>"
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
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="alert alert-info text-center">Belum ada pesanan.</div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/frontend/pesanan.blade.php ENDPATH**/ ?>
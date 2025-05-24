// resources/js/pesanan.js

// Tunggu DOM siap
document.addEventListener('DOMContentLoaded', () => {
  // Pastikan Snap.js sudah di-include di layout
  if (typeof snap === 'undefined') {
    console.error('Midtrans Snap.js belum di-load!');
    return;
  }

  const buttons = document.querySelectorAll('.btn-pay-now');
  if (!buttons.length) return;

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.pesananId;
      console.log('[Pesanan] klik Bayar Sekarang:', id);

      fetch(`/checkout/${id}/token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
      })
      .then(r => r.json())
      .then(res => {
        if (!res.snap_token) throw new Error('Token gagal dibuat');
        window.snap.pay(res.snap_token, {
          onSuccess: result => updateStatus(res.order_id_full, result.transaction_status),
          onError:   _     => Swal.fire('Error','Pembayaran gagal','error'),
          onClose:   _     => console.log('User batal bayar')
        });
      })
      .catch(err => {
        console.error('[Pesanan] fetch token error:', err);
        Swal.fire('Error', err.message, 'error');
      });
    });
  });

  function updateStatus(orderIdFull, status) {
    fetch('/checkout/status', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ order_id_full: orderIdFull, transaction_status: status })
    })
    .then(r => r.ok ? r.json() : Promise.reject())
    .then(() => {
      Swal.fire('Sukses','Pembayaran berhasil','success')
        .then(() => window.location.href = '/pesanan');
    })
    .catch(err => {
      console.error('[Pesanan] update status error:', err);
      Swal.fire('Error','Gagal update status','error');
    });
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const pesananData = localStorage.getItem('pesananTanpaResep');
  if (!pesananData) return;

  const pesanan = JSON.parse(pesananData);

  // Ambil elemen
  const daftarProdukList = document.getElementById('listProdukTanpaResep');
  const totalProdukEl = document.querySelector('.total-produk');
  const ongkirEl = document.querySelector('.total-ongkir');
  const totalBayarEl = document.querySelector('.total-bayar');

  // Kosongkan dulu daftar
  daftarProdukList.innerHTML = '';

  // Tambahkan produk
  pesanan.produk.forEach(item => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `
      <span>${item.name} - ${item.qty} pcs</span>
      <span>Rp${(item.price * item.qty).toLocaleString()}</span>
    `;
    daftarProdukList.appendChild(li);
  });

  // Tampilkan total
  totalProdukEl.innerText = `Rp${pesanan.totalProduk.toLocaleString()}`;
  ongkirEl.innerText = `Rp${pesanan.ongkir.toLocaleString()}`;
  totalBayarEl.innerText = `Rp${pesanan.totalBayar.toLocaleString()}`;

  // Tampilkan modal
  const modal = new bootstrap.Modal(document.getElementById('modalPesananNonResep'));
  modal.show();

  // Opsional: hapus dari localStorage setelah ditampilkan
  // localStorage.removeItem('pesananTanpaResep');
});
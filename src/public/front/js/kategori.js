document.addEventListener("DOMContentLoaded", function () {
  // Ambil SEMUA link kategori dari sidebar, navbar, dan footer
  const kategoriLinks = document.querySelectorAll('.kategori-list [data-kategori], .kategori-navbar [data-kategori]');
  const produkCards = document.querySelectorAll('.col-6[data-kategori]');

  kategoriLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();

      const selectedKategori = this.getAttribute('data-kategori');

      produkCards.forEach(card => {
        const cardKategori = card.getAttribute('data-kategori');
        card.style.display = (cardKategori === selectedKategori) ? 'block' : 'none';
      });

      document.querySelector('.container.mt-7')?.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // "Lihat Semua Produk" dari SEMUA lokasi (sidebar, navbar, footer)
  const lihatSemuaButtons = document.querySelectorAll('#lihat-semua, [data-lihat-semua]');
  lihatSemuaButtons.forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      produkCards.forEach(card => {
        card.style.display = 'block';
      });

      document.querySelector('.container.mt-7')?.scrollIntoView({ behavior: 'smooth' });
    });
  });
});

let cartCount = 0;
const badge = document.getElementById('cart-badge');
const tanpaResepList = document.getElementById('tanpaResepList');
const cartTotalElement = document.getElementById('cartTotal');
const cartItems = { tanpa: [] };

// Tambah item ke cart
function addToCartList(product) {
  const existing = cartItems.tanpa.find(p => p.name === product.name);
  if (existing) existing.qty++;
  else {
    product.qty = 1;
    cartItems.tanpa.push(product);
  }
  renderCartList();
  const trigger = new bootstrap.Tab(document.querySelector(`[data-bs-target="#tanpaResep"]`));
  trigger.show();
  updateCartBadge();
  updateCartTotal();
}

// Render ulang list produk
function renderCartList() {
  tanpaResepList.innerHTML = '';
  cartItems.tanpa.forEach((item, index) => {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 border-bottom pb-2';
    div.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <img src="${item.img}" alt="${item.name}" width="50" height="50" class="rounded">
        <div>
          <div class="fw-semibold small">${item.name}</div>
          <div class="text-muted small">Rp${item.price.toLocaleString()}</div>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${index}, -1)">-</button>
        <span>${item.qty}</span>
        <button class="btn btn-sm btn-outline-secondary" onclick="changeQty(${index}, 1)">+</button>
        <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">&times;</button>
      </div>
    `;
    tanpaResepList.appendChild(div);
  });

  updateCartBadge();
  updateCartTotal();
}

// Ubah jumlah
function changeQty(index, delta) {
  cartItems.tanpa[index].qty += delta;
  if (cartItems.tanpa[index].qty <= 0) cartItems.tanpa.splice(index, 1);
  renderCartList();
}

// Hapus item
function removeItem(index) {
  cartItems.tanpa.splice(index, 1);
  renderCartList();
}

// Tambah ke cart dengan animasi
function animateToCart(imageElement, product) {
  const cartIcon = document.querySelector('.bi-cart');
  const imgRect = imageElement.getBoundingClientRect();
  const clone = imageElement.cloneNode(true);
  clone.style.position = 'fixed';
  clone.style.left = imgRect.left + 'px';
  clone.style.top = imgRect.top + 'px';
  clone.style.width = imgRect.width + 'px';
  clone.style.height = imgRect.height + 'px';
  clone.style.transition = 'all 0.8s ease-in-out';
  clone.style.zIndex = '1000';
  document.body.appendChild(clone);

  const cartRect = cartIcon.getBoundingClientRect();
  setTimeout(() => {
    clone.style.left = cartRect.left + 'px';
    clone.style.top = cartRect.top + 'px';
    clone.style.width = '20px';
    clone.style.height = '20px';
    clone.style.opacity = 0;
  }, 10);

  setTimeout(() => {
    clone.remove();
    cartCount++;
    badge.style.display = 'inline-block';
    badge.innerText = cartCount;
    addToCartList(product);
  }, 800);
}

// Produk klik event
document.querySelectorAll('.card-obat').forEach(card => {
  const img = card.querySelector('img');
  const icon = card.querySelector('.cart-icon');
  const badgeText = card.querySelector('.badge').textContent.trim().toLowerCase();
  const isPerluResep = badgeText.includes('perlu');

  if (!isPerluResep && icon) {
    const name = card.querySelector('h6').textContent.trim();
    const priceText = card.querySelector('.harga-produk').textContent.trim();
    const price = parseInt(priceText.replace(/[^\d]/g, ''));
    const product = { img: img.src, name, price };

    icon.addEventListener('click', () => animateToCart(img, product));
  }
});

// Produk populer klik
document.querySelectorAll('.cart-icon-populer').forEach(button => {
  button.addEventListener('click', (event) => {
    const card = event.target.closest('.card');
    const isPerluResep = card.querySelector('.badge').textContent.trim().toLowerCase().includes('perlu');
    if (isPerluResep) return;

    const img = card.querySelector('img');
    const name = card.querySelector('.card-title').textContent.trim();
    const priceText = card.querySelector('.text-danger').textContent.trim();
    const price = parseInt(priceText.replace(/[^\d]/g, ''));
    const product = { img: img.src, name, price };

    animateToCart(img, product);
  });
});

// Tab switch
const summaryTanpa = document.getElementById('summaryTanpaResep');
const summaryPerlu = document.getElementById('summaryPerluResep');
const cartTabEl = document.querySelector('#cartTab');
if (cartTabEl) {
  cartTabEl.addEventListener('click', function (e) {
    const targetId = e.target.getAttribute('data-bs-target');
    if (!targetId) return;

    if (targetId === '#tanpaResep') {
      summaryTanpa.classList.remove('d-none');
      summaryPerlu.classList.add('d-none');
    } else if (targetId === '#perluResep') {
      summaryTanpa.classList.add('d-none');
      summaryPerlu.classList.remove('d-none');
    }
  });
}

// Update badge
function updateCartBadge() {
  const totalQty = cartItems.tanpa.reduce((total, item) => total + item.qty, 0);
  badge.innerText = totalQty;
  badge.style.display = totalQty > 0 ? 'inline-block' : 'none';
}

// Update total (produk + ongkir)
function updateCartTotal() {
  const totalProduk = cartItems.tanpa.reduce((sum, item) => sum + item.qty * item.price, 0);
  const ongkir = window.cartOngkir || 0;
  const total = totalProduk + ongkir;
  cartTotalElement.innerText = `Rp${total.toLocaleString()}`;
}

// Tangkap event ongkirUpdated dari map.js
document.addEventListener('ongkirUpdated', function () {
  updateCartTotal();
});

// Inisialisasi
document.addEventListener('DOMContentLoaded', () => {
  if (badge && cartCount === 0) badge.style.display = 'none';
  updateCartTotal();
});

// ✅ Tambahan: Checkout button listener
document.getElementById('checkoutBtn').addEventListener('click', () => {
  const pesanan = {
    produk: cartItems.tanpa,
    totalProduk: cartItems.tanpa.reduce((sum, item) => sum + item.qty * item.price, 0),
    ongkir: window.cartOngkir || 0,
    totalBayar: 0,
  };

  pesanan.totalBayar = pesanan.totalProduk + pesanan.ongkir;

  localStorage.setItem('pesananTanpaResep', JSON.stringify(pesanan));

  // Redirect ke halaman pesanan
  window.location.href = 'allpesanan.html';
});


document.getElementById('checkoutBtn').addEventListener('click', () => {
  const pesanan = {
    produk: cartItems.tanpa,
    totalProduk: cartItems.tanpa.reduce((sum, item) => sum + item.qty * item.price, 0),
    ongkir: window.cartOngkir || 0,
    totalBayar: 0,
  };

  pesanan.totalBayar = pesanan.totalProduk + pesanan.ongkir;

  // Simpan ke localStorage
  localStorage.setItem('pesananTanpaResep', JSON.stringify(pesanan));
  localStorage.setItem('pesananBaru', 'true'); // Buat trigger badge merah

  // Tampilkan popup sukses
  Swal.fire({
    icon: 'success',
    title: 'Pesanan Berhasil!',
    text: 'Pesanan sudah berhasil dibuat, silakan cek halaman pesanan.',
    confirmButtonText: 'Lihat Pesanan',
  }).then(() => {
    // Setelah klik OK, redirect
    window.location.href = 'allpesanan.html';
  });
});



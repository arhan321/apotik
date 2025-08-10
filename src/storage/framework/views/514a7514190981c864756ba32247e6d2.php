<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Apotik Alfarizi</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="<?php echo e(asset('front/style/style.css')); ?>" />
</head>
<body>

  <!-- Top Bar -->
  <div class="top-bar bg-white py-2 shadow-sm sticky-top" style="top: 0;" >
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand fw-bold text-white fs-4" href="#">Apotik Alfarizi</a>

      <div>
    <!-- Pesanan-->
<!-- Tambahkan ini di dalam tag <a> di menu Pesanan -->
  <?php if(auth()->guard()->check()): ?>
<a href="/pesanan" class="me-3 text-dark text-decoration-none position-relative">
  <i class="bi bi-clipboard-check"></i>
  <span class="d-none d-md-inline">Pesanan</span>
  <span id="badge-pesanan" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;"></span>
</a>
  <?php endif; ?>

<!-- Pengajuan -->
<?php if(auth()->guard()->check()): ?>
<a href="/pengajuan" class="me-3 text-dark text-decoration-none">
  <i class="bi bi-clipboard"></i>
  <span class="d-none d-md-inline">Pengajuan</span>
</a>  
<?php endif; ?>

<!-- Akun -->
<?php if(auth()->guard()->check()): ?>
  <a href="/profile" class="me-3 text-dark text-decoration-none">
    <i class="bi bi-person-circle"></i>
    <span class="d-none d-md-inline">Halo, <?php echo e(Auth::user()->name); ?></span>
  </a>
<?php else: ?>
  <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin" class="me-3 text-dark text-decoration-none">
    <i class="bi bi-person"></i>
    <span class="d-none d-md-inline">Akun kamu</span>
  </a>
<?php endif; ?>
        <!-- Keranjang -->
        <a href="#" class="text-dark text-decoration-none position-relative"
          data-bs-toggle="offcanvas"  data-bs-target="#cartSidebarTanpaResep">
          <i class="bi bi-cart fs-5"></i>
          <span id="cart-badge"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="display: none; font-size: 0.6rem;">
            0
          </span>
          <span class="d-none d-md-inline">Keranjang kamu</span>
        </a>

      </div>
    </div>
  </div>

  <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light py-2">
  <div class="container">

    <!-- Hamburger toggle button -->
    <button class="navbar-toggler ms-auto border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <i class="bi bi-list fs-2 text-dark"></i>
    </button>

    <!-- Navigation links -->
    <div class="collapse navbar-collapse justify-content-between align-items-center" id="navbarContent">
      <ul class="navbar-nav flex-wrap kategori-navbar">
        <li class="nav-item"><a class="nav-link fw-medium" href="/">Home</a></li>
           <li class="nav-item"><a class="nav-link fw-medium" href="#">Layanan</a></li>
        <li class="nav-item"><a class="nav-link fw-bold text-primary" href="/pesananresep">Pesan Obat Dengan Resep Dokter</a></li>
        <li class="nav-item"><a class="nav-link fw-bold text-primary" href="#producttanparesep">Pesan Obat Tanpa Resep Dokter</a></li>
      </ul>

      <!-- Banner wa ke farmasi Desktop -->
      <div class="wa-banner-desktop d-none d-lg-block ms-lg-auto">
        <div class="text-white bg-primary text-center rounded px-3 py-2 small d-inline-block">
          <strong>BINGUNG MAU SOLUSI OBAT APA?</strong><br />
          <a href="https://wa.me/6281385331107?text=Halo%20Dok%2C%20saya%20bingung%20mau%20pilih%20obat%20nih" 
             class="text-white text-decoration-underline fw-semibold" 
             target="_blank">
            Klik dan tanya di WA sini ya <i class="bi bi-whatsapp"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Banner wa ke farmasi Mobile -->
    <div class="container d-lg-none mt-2">
      <div class="wa-banner-mobile text-white bg-primary text-center rounded py-1 px-2 small">
        <strong>BINGUNG MAU SOLUSI OBAT APA?</strong><br />
        <a href="https://wa.me/6281385331107?text=Halo%20Dok%2C%20saya%20bingung%20mau%20pilih%20obat%20nih" 
           class="text-white text-decoration-underline fw-semibold" 
           target="_blank">
          Klik dan tanya di WA sini ya <i class="bi bi-whatsapp"></i>
        </a>
      </div>
    </div>
    
  </div>
</nav>

<!-- End Navbar -->


<!-- Modal Login -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 rounded-4 shadow-lg border-0">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalLoginLabel">Masuk ke Akunmu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body pt-2">
        <?php if(session('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
        <form method="POST" action="<?php echo e(route('login')); ?>">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label for="emailLogin" class="form-label">Email</label>
            <input type="email" name="email" class="form-control rounded-3" id="emailLogin" required>
          </div>
          <div class="mb-3">
            <label for="passwordLogin" class="form-label">Kata Sandi</label>
            <input type="password" name="password" class="form-control rounded-3" id="passwordLogin" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
        </form>
        <?php if(session('status')): ?>
          <div class="alert alert-danger mt-3">
            <?php echo e(session('status')); ?>

          </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
          <div class="alert alert-danger mt-3">
            <ul class="mb-0">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>
        <!-- Alert Daftar -->
      <div class="alert alert-light border mt-4 mb-0 text-center small rounded-3" role="alert">
        Belum punya akun? 
        <a href="#" class="text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRegister" data-bs-dismiss="modal">Daftar di sini ya</a>
      </div>
      </div>
    </div>
  </div>
</div>

<?php if(session('error') || $errors->any()): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('modalLogin')).show();
  });
</script>
<?php endif; ?>

<!-- Modal Register -->
<!-- Modal Register -->
<div class="modal fade" id="modalRegister" tabindex="-1" aria-labelledby="modalRegisterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="modalRegisterLabel">Daftar Akun Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body pt-0">
        <form method="POST" action="<?php echo e(route('register')); ?>">
          <?php echo csrf_field(); ?>

          <div class="mb-3">
            <label for="registerName" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="registerName" name="name" required>
          </div>

          <div class="mb-3">
            <label for="registerEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="registerEmail" name="email" required>
          </div>

          <div class="mb-3">
            <label for="registerPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="registerPassword" name="password" required>
          </div>

          <div class="mb-3">
            <label for="registerPasswordConfirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="registerPasswordConfirmation" name="password_confirmation" required>
          </div>

          <div class="mb-3">
            <label for="registerPhone" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="registerPhone" name="nomor_telepon" required>
          </div>

          <div class="mb-3">
            <label for="registerTanggalLahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="registerTanggalLahir" name="tanggal_lahir" required>
          </div>

          <div class="mb-4">
            <label for="registerGender" class="form-label">Jenis Kelamin</label>
            <select class="form-select" id="registerGender" name="jenis_kelamin" required>
              <option value="" disabled selected>Pilih jenis kelamin</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>

          <button type="submit" class="btn btn-success w-100 rounded-pill">Daftar Sekarang</button>
        </form>
        <?php if($errors->any()): ?>
          <div class="alert alert-danger small">
            <ul class="mb-0">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>
        <!-- Alert Masuk -->
        <div class="alert alert-light border mt-4 mb-0 text-center small rounded-3" role="alert">
          Sudah punya akun? 
          <a href="#" class="text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#modalLogin" data-bs-dismiss="modal">Masuk di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>


  <!-- INI MODAL CART PRODUCT YA KI -->
<!-- OFFCANVAS SIDEBAR: HANYA UNTUK TANPA RESEP -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebarTanpaResep" data-bs-scroll="true">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title">Keranjang Kamu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">

    <!-- List Produk Tanpa Resep -->
    <div id="tanpaResepList" class="flex-grow-1 overflow-auto mb-3" style="padding-bottom: 250px;">
      <!-- Produk akan di-render via JavaScript -->
    </div>

    <!-- Alamat dan Lokasi -->
    <div class="border-top pt-3">
      <hr class="my-3" />

      <?php if(auth()->guard()->check()): ?>
        <div class="mb-2 text-muted small">
          Akun: <strong><?php echo e(Auth::user()->name); ?></strong>
        </div>
      <?php endif; ?>

      <!-- Input Alamat -->
      <label for="alamatPengiriman" class="form-label fw-semibold">Alamat Pengiriman</label>
      <textarea id="alamatPengiriman" class="form-control mb-3" rows="2" placeholder="Tulis alamat lengkapmu di sini..."></textarea>

      <label class="form-label fw-semibold">Lokasi Anda & Rute ke Apotek</label>
      <div class="alert alert-danger mt-1 small fw-semibold" role="alert">
        <i class="bi bi-geo-alt-fill"></i> Aktifkan GPS atau izinkan lokasi agar ongkir muncul otomatis.
      </div>

      <div id="cartMap" style="height: 250px; border-radius: 10px;"></div>

      <div class="alert alert-warning mt-2 small">
        Gratis Ongkir jika jarak tidak lebih dari 5 km. Jika lebih, dikenakan Rp3.000 per km (dibulatkan ke atas).
      </div>

      <div id="cartOngkirInfo" class="mt-2 text-muted small fw-semibold">
        Ongkir: <span id="ongkirDisplay">-</span> | Jarak: <span id="jarakDisplay">-</span> km
      </div>
    </div>

    <!-- Total Harga dan Checkout -->
    <div class="mt-4 border-top pt-3">
      <h5 class="fw-bold mt-2">Total: <span id="cartTotal" class="float-end">Rp0</span></h5>

      <div class="alert alert-info mt-3 small" role="alert">
        Silakan checkout dan lihat statusnya di menu <strong>Pesanan</strong>.
      </div>

      <button id="checkoutBtn" class="btn btn-success w-100 mt-2">Checkout</button>
    </div>

  </div>
</div>

<?php echo $__env->yieldContent('content'); ?>


<!-- footer -->
<footer class="py-4 mt-5" style="background-color: #1e3a8a;">
  <div class="container text-white">
    <div class="row align-items-start">
      <!-- Logo dan navigasi kategori -->
      <div class="col-lg-8 mb-4 mb-lg-0">
        <h5 class="fw-bold">Apotik Alfarizi</h5>
      <ul class="navbar-nav flex-row flex-wrap mt-2 kategori-navbar">
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Obat Demam">Obat Demam</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Obat Flu">Obat Flu</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Obat Batuk">Obat Batuk</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Obat Luka">Obat Luka</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Obat Alergi">Obat Alergi</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white-50" href="#" data-kategori="Vitamin">Vitamin</a></li>
        <li class="nav-item me-3"><a class="text-decoration-none text-white fw-bold" href="#" data-lihat-semua>Semua Produk</a></li>
      </ul>

      </div>
      <!-- Banner WhatsApp -->
      <div class="col-lg-4 d-flex justify-content-lg-end justify-content-start mt-4 mt-lg-0">
        <div class="wa-banner">
          <div class="bg-white text-center rounded px-3 py-2 small d-inline-block">
            <strong class="aw" style="color: #334;">BINGUNG MAU SOLUSI OBAT APA?</strong><br />
            <a href="https://wa.me/6281385331107?text=Halo%20Dok%2C%20saya%20bingung%20mau%20pilih%20obat%20nih" 
              class="fw-semibold text-decoration-underline" 
              style="color: #25D366;" target="_blank">
              Klik dan tanya di WA sini ya <i class="bi bi-whatsapp" style="color: #25D366;"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
    <hr class="border-light my-4" />
    <div class="d-flex flex-column flex-md-row justify-content-between text-white-50 small">
      <div>&copy; 2077 Untitled UI. All rights reserved.</div>
      <div class="mt-2 mt-md-0">
        <a href="#" class="text-white-50 text-decoration-none me-3">Terms</a>
        <a href="#" class="text-white-50 text-decoration-none me-3">Privacy</a>
        <a href="#" class="text-white-50 text-decoration-none">Cookies</a>
      </div>
    </div>
  </div>
</footer>
<script>
  
</script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js" defer></script>
<script defer src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?php echo e(config('midtrans.client_key')); ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('front/js/script.js')); ?>" defer></script>
<script src="<?php echo e(asset('front/js/cart-map-checkout.js')); ?>"></script>
<script src="<?php echo e(asset('front/js/kategori.js')); ?>"></script>
<script src="<?php echo e(asset('front/js/pengajuan.js')); ?>"></script>
<script src="<?php echo e(asset('front/js/pesanan.js')); ?>"></script>

</body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/index.blade.php ENDPATH**/ ?>
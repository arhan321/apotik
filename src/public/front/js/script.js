document.addEventListener("DOMContentLoaded", function () {
  // === KATEGORI PRODUK SLIDER ===
  const productList = document.getElementById("productList");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  let scrollTimeout;

  if (productList && prevBtn && nextBtn) {
    prevBtn.addEventListener("click", () => {
      productList.scrollBy({ left: -300, behavior: 'smooth' });
      showScrollbarTemporarily();
    });

    nextBtn.addEventListener("click", () => {
      productList.scrollBy({ left: 300, behavior: 'smooth' });
      showScrollbarTemporarily();
    });

    productList.addEventListener("scroll", showScrollbarTemporarily);

    function showScrollbarTemporarily() {
      productList.classList.add("show-scrollbar");
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        productList.classList.remove("show-scrollbar");
      }, 2000);
    }
  }

  // === LOGIN REDIRECT TO PROFILE.HTML ===
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      window.location.href = "profile.html"; // pastikan nama file benar
    });
  }

  // === CART SIDEBAR ===
  const cartSidebar = document.getElementById('cartSidebar');
  if (cartSidebar) {
    cartSidebar.addEventListener('shown.bs.offcanvas', function () {
      document.body.style.overflow = 'auto';
    });
  }
});

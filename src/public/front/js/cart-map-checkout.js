let leafletMap = null;

document.addEventListener("DOMContentLoaded", function () {
  let cartCount = 0;
  const badge = document.getElementById("cart-badge");
  const tanpaResepList = document.getElementById("tanpaResepList");
  const cartTotalElement = document.getElementById("cartTotal");
  const alamatTextarea = document.getElementById("alamatPengiriman");
  const ongkirInfoEl = document.getElementById("ongkirDisplay");
  const jarakInfoEl = document.getElementById("jarakDisplay");
  const checkoutBtn = document.getElementById("checkoutBtn");

  const cartItems = { tanpa: [] };
  let cartOngkir = 0;
  let cartJarak = 0;

  function addToCartList(product) {
    const existing = cartItems.tanpa.find((p) => p.obat_id === product.obat_id);
    if (existing) existing.qty++;
    else {
      product.qty = 1;
      cartItems.tanpa.push(product);
    }
    renderCartList();
    updateCartBadge();
    updateCartTotal();
  }

  function renderCartList() {
    tanpaResepList.innerHTML = "";
    cartItems.tanpa.forEach((item, index) => {
      const div = document.createElement("div");
      div.className = "d-flex align-items-center justify-content-between mb-2 border-bottom pb-2";
      div.innerHTML = `
        <div class="d-flex align-items-center gap-2">
          <img src="${item.img}" alt="${item.name}" width="50" height="50" class="rounded">
          <div>
            <div class="fw-semibold small">${item.name}</div>
            <div class="text-muted small">Rp${item.price.toLocaleString()}</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary" onclick="window.changeQty(${index}, -1)">-</button>
          <span>${item.qty}</span>
          <button class="btn btn-sm btn-outline-secondary" onclick="window.changeQty(${index}, 1)">+</button>
          <button class="btn btn-sm btn-danger" onclick="window.removeItem(${index})">&times;</button>
        </div>
      `;
      tanpaResepList.appendChild(div);
    });
    updateCartBadge();
    updateCartTotal();
  }

  window.changeQty = function (index, delta) {
    cartItems.tanpa[index].qty += delta;
    if (cartItems.tanpa[index].qty <= 0) cartItems.tanpa.splice(index, 1);
    renderCartList();
  };

  window.removeItem = function (index) {
    cartItems.tanpa.splice(index, 1);
    renderCartList();
  };

  function updateCartBadge() {
    const totalQty = cartItems.tanpa.reduce((total, item) => total + item.qty, 0);
    badge.innerText = totalQty;
    badge.style.display = totalQty > 0 ? "inline-block" : "none";
  }

  function updateCartTotal() {
    const totalProduk = cartItems.tanpa.reduce((sum, item) => sum + item.qty * item.price, 0);
    const total = totalProduk + cartOngkir;
    cartTotalElement.innerText = `Rp${total.toLocaleString()}`;
  }

  document.querySelectorAll(".card-obat").forEach((card) => {
    const img = card.querySelector("img");
    const icon = card.querySelector(".cart-icon");
    const badgeText = card.querySelector(".badge")?.textContent?.trim().toLowerCase();
    const isPerluResep = badgeText?.includes("perlu");
    const obatId = card.dataset.obatid;

    if (!isPerluResep && icon && obatId) {
      const name = card.querySelector("h6").textContent.trim();
      const priceText = card.querySelector(".harga-produk").textContent.trim();
      const price = parseInt(priceText.replace(/[^\d]/g, ""));
      const product = { img: img.src, name, price, obat_id: parseInt(obatId) };
      icon.addEventListener("click", () => addToCartList(product));
    }
  });

  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", () => {
      const alamat = alamatTextarea.value.trim();
      if (!alamat) return alert("Alamat harus diisi.");

      const cart = cartItems.tanpa.map((item) => ({ obat_id: item.obat_id, qty: item.qty }));

      fetch("/checkout", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
        },
        body: JSON.stringify({ cart, ongkir: cartOngkir, alamat, jarak: cartJarak }),
      })
        .then((res) => res.json().then((data) => {
          if (!res.ok) throw new Error(data.message || "Terjadi kesalahan.");
          return data;
        }))
        .then((res) => {
          if (res.snap_token) {
            window.snap.pay(res.snap_token, {
              onSuccess: function (result) {
                fetch('/checkout/status', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").content,
                  },
                  body: JSON.stringify({
                    order_id_full: res.order_id_full,
                    transaction_status: result.transaction_status,
                  }),
                })
                  .then((r) =>
                    r.ok
                      ? Swal.fire('Pembayaran Berhasil', 'Pesanan sedang diproses', 'success').then(() => (window.location.href = '/pesanan'))
                      : Promise.reject()
                  )
                  .catch(() => Swal.fire('Error', 'Gagal memperbarui status pesanan', 'error'));
              },
            });
          }
        })
        .catch((err) => {
          console.error('Gagal fetch /checkout:', err);
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: err.message || 'Terjadi kesalahan saat menghubungi server.',
          });
        });
    });
  }

  const sidebar = document.getElementById("cartSidebarTanpaResep");
  if (sidebar) {
    sidebar.addEventListener("shown.bs.offcanvas", function () {
      initMapAndRoute("cartMap");
    });
  }

  function initMapAndRoute(mapId = "cartMap") {
    const mapElement = document.getElementById(mapId);
    if (!mapElement) return;

    if (leafletMap !== null) {
      leafletMap.remove();
    }

    const tokoLat = -6.173366640005029;
    const tokoLng = 106.56471401711408;

    leafletMap = L.map(mapId).setView([tokoLat, tokoLng], 13);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(leafletMap);

    const apotekIcon = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/149/149060.png",
      iconSize: [36, 36],
      iconAnchor: [18, 36],
    });

    const userIcon = L.icon({
      iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
      iconSize: [36, 36],
      iconAnchor: [18, 36],
    });

    L.marker([tokoLat, tokoLng], { icon: apotekIcon }).addTo(leafletMap).bindPopup("Lokasi Apotek");

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          const userLat = position.coords.latitude;
          const userLng = position.coords.longitude;

          L.marker([userLat, userLng], { icon: userIcon }).addTo(leafletMap).bindPopup("Lokasi Anda");

          fetch(
            `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${tokoLng},${tokoLat}?overview=full&geometries=geojson`
          )
            .then((res) => res.json())
            .then((data) => {
              if (!data.routes?.length) return;
              const route = data.routes[0];
              const rawDistanceKm = route.distance / 1000;
              const roundedKm = Math.round(rawDistanceKm);
              cartJarak = roundedKm;
              const MIN_FREE_KM = 5;
              const RATE_PER_KM = 3000;
              let ongkir = 0;
              if (roundedKm > MIN_FREE_KM) {
                ongkir = (roundedKm - MIN_FREE_KM) * RATE_PER_KM;
              }
              cartOngkir = ongkir;
              ongkirInfoEl.textContent = `Rp${cartOngkir.toLocaleString("id-ID")}`;
              jarakInfoEl.textContent = `${cartJarak} km`;

              // Reverse geocoding ke alamat manusiawi
              fetch(
                `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${userLat}&lon=${userLng}`
              )
                .then((res) => res.json())
                .then((loc) => {
                  alamatTextarea.value = loc.display_name;
                })
                .catch(() => {
                  alamatTextarea.value = `${userLat.toFixed(5)}, ${userLng.toFixed(5)}`;
                });

              updateCartTotal();
            });
        },
        (err) => {
          alert("Gagal mendeteksi lokasi: " + err.message);
        }
      );
    } else {
      alert("Geolocation tidak didukung oleh browser Anda.");
    }

    setTimeout(() => leafletMap.invalidateSize(), 300);
  }
});

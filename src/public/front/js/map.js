function initMapAndRoute(mapId = 'map') {
  const mapElement = document.getElementById(mapId);
  if (!mapElement) return;

  const tokoLat = -6.246761;
  const tokoLng = 106.729114;
  const map = L.map(mapId).setView([tokoLat, tokoLng], 13);
  const ongkirId = (mapId === 'map') ? 'ongkirInfo' : 'cartOngkirInfo';
  const ongkirElement = document.getElementById(ongkirId);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const apotekIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149060.png',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
  });

  const userIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
  });

  L.marker([tokoLat, tokoLng], { icon: apotekIcon }).addTo(map).bindPopup("Lokasi Apotek").openPopup();

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;

      L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup("Lokasi Anda");

      fetch(`https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${tokoLng},${tokoLat}?overview=full&geometries=geojson`)
        .then(res => res.json())
        .then(data => {
          if (data.routes && data.routes.length > 0) {
            const route = data.routes[0];
            L.geoJSON(route.geometry, {
              style: { color: 'blue', weight: 4 }
            }).addTo(map);

            map.fitBounds([
              [userLat, userLng],
              [tokoLat, tokoLng]
            ]);

            // logic gratis ongkir kalo 5 km di atas itu bayar puqi

           const distanceInKm = route.distance / 1000;

            let ongkir = 0;
            if (distanceInKm > 5) {
            const chargeableKm = Math.ceil(distanceInKm - 5); // km di atas 5 km
            ongkir = chargeableKm * 3000;
            }

            if (ongkirElement) {
              ongkirElement.textContent = `Ongkir: Rp${ongkir.toLocaleString('id-ID')} (${distanceInKm.toFixed(2)} km)`;
            }

            // Kirim ongkir ke global
            window.cartOngkir = ongkir;

            // Beritahu cart.js bahwa ongkir sudah tersedia
            document.dispatchEvent(new CustomEvent('ongkirUpdated', {
              detail: { ongkir }
            }));
          }
        });
    }, function (err) {
      alert("Gagal mendeteksi lokasi: " + err.message);
    });
  } else {
    alert("Geolocation tidak didukung di browser kamu.");
  }

  setTimeout(() => map.invalidateSize(), 300);
}

// Inisialisasi map saat DOM ready
document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("map")) {
    initMapAndRoute("map");
  }

  const sidebar = document.getElementById("cartSidebarTanpaResep");
  if (sidebar) {
    sidebar.addEventListener("shown.bs.offcanvas", function () {
      initMapAndRoute("cartMap");
    });
  }
});

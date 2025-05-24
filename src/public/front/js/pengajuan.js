document.addEventListener("DOMContentLoaded", function () {
  let leafletMap = null;

  const tokoLat = -6.246761;
  const tokoLng = 106.729114;

  const jarakInfoEl = document.getElementById("jarakInfoEl");
  const ongkirInfoEl = document.getElementById("ongkirInfoEl");
  const hiddenJarak = document.getElementById("hiddenJarak");
  const hiddenOngkir = document.getElementById("hiddenOngkir");
  const hiddenAlamat = document.getElementById("hiddenAlamat");

  if (!document.getElementById("map")) return;

  leafletMap = L.map("map").setView([tokoLat, tokoLng], 13);

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

  L.marker([tokoLat, tokoLng], { icon: apotekIcon })
    .addTo(leafletMap)
    .bindPopup("Lokasi Apotek");

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;

        L.marker([userLat, userLng], { icon: userIcon })
          .addTo(leafletMap)
          .bindPopup("Lokasi Anda");

        fetch(
          `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${tokoLng},${tokoLat}?overview=full&geometries=geojson`
        )
          .then((res) => res.json())
          .then((data) => {
            if (data.routes?.length) {
              const route = data.routes[0];
              L.geoJSON(route.geometry, { style: { color: "blue", weight: 4 } }).addTo(leafletMap);
              leafletMap.fitBounds([
                [userLat, userLng],
                [tokoLat, tokoLng],
              ]);

              const distanceInKm = route.distance / 1000;
              const jarak = parseFloat(distanceInKm.toFixed(2));
              const ongkir = jarak > 5 ? Math.ceil(jarak - 5) * 3000 : 0;

              jarakInfoEl.textContent = `${jarak} km`;
              ongkirInfoEl.textContent = `Rp${ongkir.toLocaleString("id-ID")}`;
              hiddenJarak.value = jarak;
              hiddenOngkir.value = ongkir;

              // Reverse geocoding untuk alamat manusiawi
              fetch(
                `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${userLat}&lon=${userLng}`
              )
                .then((res) => res.json())
                .then((loc) => {
                  hiddenAlamat.value = loc.display_name;
                })
                .catch(() => {
                  // fallback jika gagal reverse geocode
                  hiddenAlamat.value = `${userLat.toFixed(5)}, ${userLng.toFixed(5)}`;
                });
            }
          });
      },
      function (err) {
        alert("Gagal mendapatkan lokasi: " + err.message);
      }
    );
  } else {
    alert("Geolocation tidak didukung oleh browser Anda.");
  }

  setTimeout(() => leafletMap.invalidateSize(), 300);
});

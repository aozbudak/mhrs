{{-- Kurum ekle/düzenle formları: Leaflet ile konum seçimi + sunucu üzerinden adres araması (Nominatim) --}}
@php
    $suffix = $suffix ?? 'kurum';
@endphp

<div class="rounded-2xl border border-sky-200/80 bg-sky-50/40 p-4 space-y-3">
    <div>
        <h3 class="text-xs font-extrabold text-slate-900">Konum (harita)</h3>
        <p class="mt-1 text-[11px] leading-relaxed text-slate-600">
            Haritadan işaretçiyi sürükleyerek veya tıklayarak <strong class="font-semibold text-slate-800">tam giriş noktasını</strong> seçin.
            «Adresten konum bul» ile enlem/boylam alanlarını otomatik doldurabilirsiniz; gerekirse haritada ince ayar yapın.
        </p>
    </div>
    <div id="kurumMap-{{ $suffix }}" class="h-64 w-full overflow-hidden rounded-2xl border border-sky-200 bg-white shadow-inner sm:h-72" role="presentation"></div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" id="kurumGeocodeBtn-{{ $suffix }}"
                class="rounded-2xl border border-sky-300 bg-white px-4 py-2 text-xs font-semibold text-sky-900 shadow-sm hover:bg-sky-50 transition">
            Adresten konum bul
        </button>
        <span id="kurumGeocodeMsg-{{ $suffix }}" class="text-[11px] text-slate-600" aria-live="polite"></span>
    </div>
</div>

@push('scripts')
@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endonce
<script>
(function () {
    var suffix = @json($suffix);
    var geocodeUrl = @json(route('admin.kurum-konum-ara'));
    var mapEl = document.getElementById('kurumMap-' + suffix);
    var btn = document.getElementById('kurumGeocodeBtn-' + suffix);
    var msg = document.getElementById('kurumGeocodeMsg-' + suffix);
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var nameInput = document.getElementById('name');
    var cityInput = document.getElementById('city');
    var addressInput = document.getElementById('address');
    if (!mapEl || !btn || !latInput || !lngInput || typeof L === 'undefined') return;

    function parseCoord(v) {
        if (v === null || v === undefined) return null;
        var s = String(v).trim().replace(',', '.');
        if (!s) return null;
        var n = parseFloat(s);
        return Number.isFinite(n) ? n : null;
    }

    var startLat = parseCoord(latInput.value);
    var startLng = parseCoord(lngInput.value);
    var center = (startLat !== null && startLng !== null) ? [startLat, startLng] : [39.92, 32.85];
    var zoom = (startLat !== null && startLng !== null) ? 16 : 6;

    var map = L.map(mapEl, { scrollWheelZoom: false }).setView(center, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = L.marker(center, { draggable: true }).addTo(map);

    function setFromLatLng(lat, lng, fit) {
        latInput.value = String(Math.round(lat * 1e6) / 1e6);
        lngInput.value = String(Math.round(lng * 1e6) / 1e6);
        marker.setLatLng([lat, lng]);
        if (fit) {
            map.setView([lat, lng], Math.max(map.getZoom(), 15));
        }
    }

    marker.on('dragend', function (e) {
        var ll = e.target.getLatLng();
        setFromLatLng(ll.lat, ll.lng, false);
    });

    map.on('click', function (e) {
        setFromLatLng(e.latlng.lat, e.latlng.lng, false);
    });

    if (startLat !== null && startLng !== null) {
        setFromLatLng(startLat, startLng, false);
    }

    setTimeout(function () { map.invalidateSize(); }, 200);

    btn.addEventListener('click', function () {
        if (msg) msg.textContent = 'Aranıyor…';
        var params = new URLSearchParams();
        if (nameInput && nameInput.value) params.set('name', nameInput.value);
        if (addressInput && addressInput.value) params.set('address', addressInput.value);
        if (cityInput && cityInput.value) params.set('city', cityInput.value);
        fetch(geocodeUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (res) {
                if (!res.ok) {
                    if (msg) msg.textContent = (res.j && res.j.message) ? res.j.message : 'Arama başarısız.';
                    return;
                }
                var j = res.j;
                if (typeof j.lat !== 'number' || typeof j.lng !== 'number') {
                    if (msg) msg.textContent = 'Sonuç alınamadı.';
                    return;
                }
                setFromLatLng(j.lat, j.lng, true);
                if (msg) msg.textContent = j.label ? ('Bulundu: ' + j.label) : 'Konum güncellendi.';
            })
            .catch(function () {
                if (msg) msg.textContent = 'Ağ hatası; tekrar deneyin.';
            });
    });
})();
</script>
@endpush

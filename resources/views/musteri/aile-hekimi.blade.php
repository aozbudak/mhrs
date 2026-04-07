@extends('layouts.musteri')

@section('title', 'Aile hekimi')

@section('content')
    @php
        $ah = $user->aileHekimi;
        $hasChoice = $user->aile_hekimi_doctor_id && $ah;
    @endphp

    <div class="mb-8 overflow-hidden rounded-3xl border border-violet-100/90 bg-gradient-to-br from-white/95 via-violet-50/40 to-emerald-50/30 p-5 shadow-md surface-elevated sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-violet-200/80 bg-white/95 shadow-sm sm:flex" aria-hidden="true">
                    <svg class="h-8 w-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-violet-700">Hasta işlemleri</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-sky-950">Aile hekimi</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                        İl ve ilçenizi seçin; size yakın kayıtlı aile hekimleri listelenir. Birini işaretleyip <strong class="font-semibold text-slate-800">kaydedin</strong>. İstediğiniz zaman buradan güncelleyebilir veya seçimi kaldırabilirsiniz.
                    </p>
                </div>
            </div>
            <a href="{{ route('musteri.randevu.al') }}"
               class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25">
                Randevu al
            </a>
        </div>
    </div>

    <div class="mb-8 rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm sm:p-6">
        <h2 class="text-sm font-extrabold text-slate-900">Aile hekimi seç veya güncelle</h2>
        <p class="mt-1 text-xs text-slate-600">Listede görünen hekimler yönetici tarafından “aile hekimi” olarak işaretlenmiş aktif kayıtlardır.</p>

        <form method="post" action="{{ route('musteri.aile-hekimi.kaydet') }}" class="mt-5 space-y-4" id="form-aile-hekimi-kaydet">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="patient_city" class="mb-1 block text-sm font-medium text-slate-700">İl</label>
                    <select name="patient_city" id="patient_city" required
                            class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                        <option value="">İl seçin</option>
                        @foreach ($cities as $c)
                            <option value="{{ $c }}" @selected(old('patient_city', $user->patient_city) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('patient_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div id="patient_district_wrap">
                    <label for="patient_district" class="mb-1 block text-sm font-medium text-slate-700">İlçe</label>
                    <select name="patient_district" id="patient_district"
                            class="w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                        <option value="">Önce il seçin</option>
                    </select>
                    <p id="patient_district_hint" class="mt-1 hidden text-xs text-slate-500">Bu il için ilçe listesi yok; öneriler il geneline göre yapılır.</p>
                    @error('patient_district')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Yakın aile hekimleri</p>
                <div id="aile-hekim-list" class="min-h-[3rem] rounded-2xl border border-dashed border-sky-200 bg-sky-50/40 px-3 py-3 text-sm text-slate-600">
                    İl (ve varsa ilçe) seçtikten sonra liste burada görünür.
                </div>
                @error('aile_hekimi_doctor_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-4 focus:ring-violet-500/25 sm:w-auto">
                Seçimi kaydet
            </button>
        </form>
    </div>

    @if ($hasChoice)
        @php
            $doctorName = $ah->user?->name ?? trim((string) $ah->title) ?: 'İsimsiz kayıt';
            $inactive = ! $ah->is_active || ! $ah->is_aile_hekimi;
        @endphp
        <div class="mb-8 rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-6 shadow-sm surface-elevated">
            <h2 class="text-sm font-extrabold text-slate-900">Kayıtlı seçiminiz</h2>
            <div class="mt-4 flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0 flex-1 space-y-3">
                    @if ($user->patient_city || $user->patient_district)
                        <p class="text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">Bölge:</span>
                            {{ $user->patient_city ?? '—' }}
                            @if ($user->patient_district)
                                <span class="text-slate-400">·</span> {{ $user->patient_district }}
                            @endif
                        </p>
                    @endif
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Aile hekimi</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $doctorName }}</p>
                        @if ($ah->title)
                            <p class="text-sm text-slate-600">{{ $ah->title }}</p>
                        @endif
                    </div>
                    <dl class="grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Birim</dt>
                            <dd class="mt-0.5 text-slate-800">{{ $ah->department?->name ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hastane</dt>
                            <dd class="mt-0.5 text-slate-800">{{ $ah->hospital?->name ?? '—' }}</dd>
                            @if ($ah->hospital?->address)
                                <dd class="mt-1 text-xs text-slate-600 leading-relaxed">{{ $ah->hospital->address }}</dd>
                            @endif
                            @if ($ah->hospital?->phone)
                                <dd class="mt-1 text-xs text-slate-600">Tel: {{ $ah->hospital->phone }}</dd>
                            @endif
                        </div>
                    </dl>
                    @if ($inactive)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/90 px-3 py-2 text-xs font-medium text-amber-950">
                            Bu kayıt artık aktif aile hekimi olarak işaretli değil veya hesap pasif. Yukarıdan yeni bir seçim yapabilirsiniz.
                        </div>
                    @endif
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                    @if ($ah->hospital_id && $ah->department_id && $ah->is_active)
                        <a href="{{ route('musteri.randevu.al', ['aile_hekimi' => 1]) }}"
                           class="inline-flex w-full justify-center rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 transition sm:w-auto">
                            Bu hekimle randevu
                        </a>
                    @endif
                    <form method="post" action="{{ route('musteri.aile-hekimi.kaldir') }}" class="inline"
                          onsubmit="return confirm('Aile hekimi ve bölge seçiminiz silinecek. Emin misiniz?');">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-2xl border border-red-200 bg-red-50/90 px-4 py-2.5 text-sm font-semibold text-red-800 hover:bg-red-100/90 transition sm:w-auto">
                            Seçimi kaldır
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        (function () {
            var routes = {
                ilceler: @json(route('musteri.aile-hekimi.ilceler')),
                oneri: @json(route('musteri.aile-hekimi.oneri'))
            };
            var initialCity = @json(old('patient_city', $user->patient_city));
            var initialDistrict = @json(old('patient_district', $user->patient_district));
            var initialDoctorId = @json(old('aile_hekimi_doctor_id', $user->aile_hekimi_doctor_id));

            var selCity = document.getElementById('patient_city');
            var selDistrict = document.getElementById('patient_district');
            var districtHint = document.getElementById('patient_district_hint');
            var listEl = document.getElementById('aile-hekim-list');
            var form = document.getElementById('form-aile-hekimi-kaydet');

            if (!selCity || !selDistrict || !listEl || !form) return;

            function escapeHtml(s) {
                var div = document.createElement('div');
                div.textContent = s;
                return div.innerHTML;
            }

            function setListMessage(msg) {
                listEl.textContent = msg;
            }

            function renderDoctors(rows) {
                listEl.innerHTML = '';
                if (!rows || !rows.length) {
                    setListMessage('Bu kriterlere uygun kayıtlı aile hekimi bulunamadı.');
                    return;
                }
                var wrap = document.createElement('div');
                wrap.className = 'space-y-2';
                rows.forEach(function (d) {
                    var label = document.createElement('label');
                    label.className = 'flex cursor-pointer items-start gap-3 rounded-xl border border-sky-100 bg-white px-3 py-2.5 hover:border-violet-200';
                    var input = document.createElement('input');
                    input.type = 'radio';
                    input.name = 'aile_hekimi_doctor_id';
                    input.value = String(d.id);
                    input.required = true;
                    input.className = 'mt-1 h-4 w-4 border-sky-300 text-violet-600 focus:ring-violet-500';
                    if (initialDoctorId && String(initialDoctorId) === String(d.id)) {
                        input.checked = true;
                    }
                    var span = document.createElement('span');
                    span.className = 'text-sm text-slate-800';
                    var distPart = d.distance_km != null ? ' · yaklaşık ' + d.distance_km + ' km' : '';
                    span.innerHTML = '<span class="font-semibold">' + escapeHtml(d.name) + '</span>' +
                        '<span class="block text-xs text-slate-600">' + escapeHtml(d.hospital_name) + distPart + '</span>';
                    label.appendChild(input);
                    label.appendChild(span);
                    wrap.appendChild(label);
                });
                listEl.appendChild(wrap);
            }

            function loadDistricts() {
                var city = selCity.value;
                selDistrict.innerHTML = '';
                districtHint.classList.add('hidden');
                selDistrict.disabled = !city;
                if (!city) {
                    selDistrict.appendChild(new Option('Önce il seçin', ''));
                    setListMessage('İl seçtikten sonra ilçe ve aile hekimi listesi yüklenir.');
                    return;
                }
                fetch(routes.ilceler + '?city=' + encodeURIComponent(city), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                    .then(function (x) {
                        if (!x.ok) {
                            selDistrict.appendChild(new Option('—', ''));
                            return;
                        }
                        var districts = x.j.districts || [];
                        if (!districts.length) {
                            districtHint.classList.remove('hidden');
                            selDistrict.disabled = true;
                            selDistrict.appendChild(new Option('İl geneli', ''));
                            loadAileHekimleri();
                            return;
                        }
                        selDistrict.disabled = false;
                        selDistrict.appendChild(new Option('İlçe seçin', ''));
                        districts.forEach(function (d) {
                            var opt = document.createElement('option');
                            opt.value = d;
                            opt.textContent = d;
                            if (initialDistrict && initialDistrict === d) {
                                opt.selected = true;
                            }
                            selDistrict.appendChild(opt);
                        });
                        if (selDistrict.value) {
                            loadAileHekimleri();
                        } else {
                            setListMessage('İlçe seçtikten sonra liste yüklenir.');
                        }
                    })
                    .catch(function () {
                        setListMessage('İlçe listesi yüklenemedi.');
                    });
            }

            function loadAileHekimleri() {
                var city = selCity.value;
                if (!city) return;
                var district = selDistrict.disabled ? '' : selDistrict.value;
                if (!selDistrict.disabled && !district) {
                    setListMessage('İlçe seçtikten sonra liste yüklenir.');
                    return;
                }
                listEl.textContent = 'Yükleniyor…';
                var q = 'city=' + encodeURIComponent(city) + '&district=' + encodeURIComponent(district);
                fetch(routes.oneri + '?' + q, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                    .then(function (x) {
                        if (!x.ok) {
                            setListMessage(x.j.message || 'Liste yüklenemedi.');
                            return;
                        }
                        renderDoctors(x.j.doctors || []);
                    })
                    .catch(function () {
                        setListMessage('Aile hekimi listesi yüklenemedi.');
                    });
            }

            selCity.addEventListener('change', function () {
                initialDistrict = '';
                initialDoctorId = null;
                loadDistricts();
            });
            selDistrict.addEventListener('change', function () {
                initialDoctorId = null;
                loadAileHekimleri();
            });

            if (initialCity && selCity.value === initialCity) {
                loadDistricts();
            }
        })();
    </script>
@endsection

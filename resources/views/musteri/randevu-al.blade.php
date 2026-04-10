@extends('layouts.musteri')

@section('title', 'Randevu Al')

@section('content')
    @php
        $aileHekimiOdak = $aileHekimiOdak ?? false;
        $randevuAlStickyParams = $randevuAlStickyParams ?? [];
        $vekaletRandevuHastalar = $vekaletRandevuHastalar ?? collect();
        $kendimRandevuUrl = $kendimRandevuUrl ?? route('musteri.randevu.al');
        $proxyQ = array_filter([
            'hasta_id' => ! empty($proxyHastaId) ? (int) $proxyHastaId : null,
            'kurum_tipi' => $kurumTipi ?? null,
        ]);
        $ahMerge = fn (array $params) => array_filter(array_merge($params, $randevuAlStickyParams));
        $ru = fn (array $params = []) => route('musteri.randevu.al', array_filter(array_merge($params, $proxyQ)));
        $randevuAlGenelUrl = route('musteri.randevu.al', array_filter([
            'hasta_id' => ! empty($proxyHastaId) ? (int) $proxyHastaId : null,
        ]));
        if ($aileHekimiOdak) {
            $activeStep = 4;
        } else {
            $activeStep = 1;
            if (! empty($hospitalId)) {
                $activeStep = 2;
            }
            if (! empty($departmentId)) {
                $activeStep = 3;
            }
            if (! empty($doctorId)) {
                $activeStep = 4;
            }
        }
        $selectedDateValue = $selectedDate?->format('Y-m-d') ?? '';
    @endphp

    <div class="w-full max-w-4xl space-y-3">
    <div class="mb-0 rounded-2xl border border-sky-100/80 bg-white/75 hospital-glass p-4 shadow-sm surface-elevated ui-soft-rise sm:p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="relative">
                <div class="absolute -left-1 top-0 h-9 w-1 rounded-full bg-gradient-to-b from-sky-500 to-emerald-500 opacity-90" aria-hidden="true"></div>
                <h1 class="pl-3 text-xl font-extrabold tracking-tight text-sky-950 sm:text-2xl">
                    @if($aileHekimiOdak) Aile hekiminizle randevu @else Randevu Al @endif
                </h1>
                @if($aileHekimiOdak)
                    <p class="mt-1.5 max-w-xl pl-3 text-xs text-slate-600">
                        Tarih ve saat seçin.
                        <a href="{{ $ru([]) }}" class="font-semibold text-emerald-700 underline decoration-emerald-300 hover:text-emerald-900">Tam akış</a>
                    </p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 rounded-2xl border border-sky-100/90 bg-sky-50/40 p-3 sm:p-2 sm:bg-transparent sm:border-0 sm:p-0" role="list" aria-label="Randevu adımları">
                @if($aileHekimiOdak)
                    <div class="flex items-center gap-2" role="listitem">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-emerald-50 text-sm font-extrabold text-violet-950 shadow-sm ring-4 ring-emerald-500/25">
                            1
                        </span>
                        <span class="text-sm font-semibold text-sky-950">Tarih &amp; saat</span>
                    </div>
                @else
                    @php
                        $steps = [
                            [1, 'İl / İlçe / Kurum'],
                            [2, 'Birim'],
                            [3, 'Doktor'],
                            [4, 'Tarih & Saat'],
                        ];
                    @endphp
                    @foreach($steps as $s)
                        <div class="flex items-center gap-2" role="listitem">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-white/80 text-sm font-extrabold text-sky-950 shadow-sm
                                @if($activeStep === $s[0]) ring-4 ring-emerald-500/25 bg-gradient-to-br from-emerald-50 to-sky-50 text-emerald-950 @endif">
                                {{ $s[0] }}
                            </span>
                            <span class="hidden sm:inline text-sm font-semibold text-slate-700 @if($activeStep === $s[0]) text-sky-950 @endif">
                                {{ $s[1] }}
                            </span>
                        </div>
                        @if($s[0] !== 4)
                            <span class="hidden h-10 w-px bg-sky-200/80 sm:block" aria-hidden="true"></span>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @if (($kurumTipi ?? null) === 'saglik_merkezi' && ! $aileHekimiOdak)
        <div class="health-center-spotlight hospital-glass pl-5 pr-4 py-4 sm:pl-6 sm:pr-5 sm:py-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <div class="min-w-0 pl-1">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-teal-800">Sağlık merkezi randevusu</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 sm:text-base">Poliklinik ve tanı-tedavi birimlerinden uygun zamanı seçin</p>
                    <p class="mt-2 max-w-2xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                        Sağlık merkezleri genelde daha küçük ölçekli kurumlardır; muayene, kontrol ve bazı poliklinik hizmetleri için randevu bu akıştan alınır.
                        Hastane randevusu için menüden genel <span class="font-semibold text-sky-800">Randevu Al</span> bağlantısını kullanabilirsiniz.
                    </p>
                </div>
                <a href="{{ $randevuAlGenelUrl }}"
                   class="ui-focus-ring ui-soft-rise shrink-0 self-start rounded-2xl border border-sky-200/90 bg-white/90 px-4 py-2.5 text-center text-xs font-bold text-sky-900 shadow-sm hover:border-sky-300 sm:self-center">
                    Tüm kurum türlerine dön
                </a>
            </div>
        </div>
    @endif

    @if ($vekaletRandevuHastalar->isNotEmpty())
        <div class="rounded-2xl border border-sky-200/90 bg-white/90 px-3 py-3 shadow-sm hospital-glass" role="navigation" aria-label="Randevu kimin için">
            <p class="text-xs font-bold uppercase tracking-wide text-sky-800">Randevu kimin için?</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <a href="{{ $kendimRandevuUrl }}"
                   class="inline-flex items-center gap-1.5 rounded-2xl border px-3 py-2 text-xs font-bold transition
                        @if(empty($proxyHastaId))
                            border-emerald-500 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-500/30 shadow-sm
                        @else
                            border-sky-200 bg-white text-slate-700 hover:border-emerald-300 hover:bg-emerald-50/50
                        @endif">
                    Kendim
                </a>
                @foreach ($vekaletRandevuHastalar as $vh)
                    @php
                        $vhUrl = route('musteri.randevu.al', array_filter([
                            'hasta_id' => $vh->id,
                            'aile_hekimi' => $aileHekimiOdak ? 1 : null,
                            'gizli_randevu' => ($gizliRandevuModu ?? false) ? 1 : null,
                        ]));
                        $vhAktif = ! empty($proxyHastaId) && (int) $proxyHastaId === (int) $vh->id;
                    @endphp
                    <a href="{{ $vhUrl }}"
                       class="inline-flex flex-col items-start gap-0.5 rounded-2xl border px-3 py-2 text-left text-xs font-bold transition sm:inline-flex sm:flex-row sm:items-center sm:gap-2
                            @if($vhAktif)
                                border-emerald-500 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-500/30 shadow-sm
                            @else
                                border-sky-200 bg-white text-slate-800 hover:border-emerald-300 hover:bg-emerald-50/50
                            @endif">
                        <span>{{ $vh->name }}</span>
                        <span class="rounded-full bg-sky-200/80 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-sky-900">
                            Yetkili
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if (! empty($proxyHastaId) && isset($randevuHedefHasta))
        <div class="rounded-2xl border border-indigo-200/90 bg-indigo-50/60 px-3 py-2 text-sm text-indigo-950 shadow-sm">
            <span class="font-semibold">{{ $randevuHedefHasta->name }}</span> adına randevu alıyorsunuz.
            <a href="{{ route('musteri.randevu.al') }}" class="ml-2 font-semibold text-indigo-800 underline decoration-indigo-300 hover:text-indigo-950">Kendi adıma randevu al</a>
        </div>
    @endif

    <div class="space-y-4">
        @unless ($aileHekimiOdak)
        <!-- Step 1: City → District → Institution -->
        <section class="rounded-2xl border border-sky-100/80 bg-white/75 hospital-glass p-4 shadow-sm surface-elevated ui-soft-rise">
            <div class="mb-3 flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                    1
                </span>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">İl, ilçe ve kurum</h2>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-emerald-200/90 bg-gradient-to-br from-emerald-50/80 to-sky-50/40 p-3 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-emerald-950">Konumuma göre sırala</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" id="mhrs-near-me-btn"
                                    data-randevu-url="{{ $ru([]) }}"
                                    class="ui-focus-ring inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                                Konumuma göre sırala
                            </button>
                            @if (!empty($nearMeActive))
                                <a href="{{ $ru(array_filter(['city' => $city, 'district' => $district ?? null, 'hospital_q' => $hospitalQ ?? null])) }}"
                                   class="rounded-2xl border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-sky-50 transition">
                                    Konum sıralamasını kapat
                                </a>
                            @endif
                        </div>
                    </div>
                    <p id="mhrs-near-me-status" class="mt-2 hidden text-xs font-medium text-amber-900" role="status" aria-live="polite"></p>
                </div>

                <form method="get" action="{{ $ru([]) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="min-w-0 flex-1">
                        <label for="randevu_il" class="mb-1 block text-sm font-medium text-slate-700">İl</label>
                        <select name="city" id="randevu_il" class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" onchange="this.form.submit()">
                            <option value="">İl seçin</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c }}" @selected($city === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($cities->isEmpty())
                        <p class="text-sm text-amber-800">Sistemde aktif hastane ve il bilgisi yok. Yönetim panelinden hastane ekleyip il ve ilçe alanlarını doldurun.</p>
                    @endif
                </form>

                @if ($city)
                    <form method="get" action="{{ $ru([]) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <input type="hidden" name="city" value="{{ $city }}">
                        <div class="min-w-0 flex-1">
                            <label for="randevu_ilce" class="mb-1 block text-sm font-medium text-slate-700">İlçe</label>
                            <select name="district" id="randevu_ilce" class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" onchange="this.form.submit()">
                                <option value="">İlçe seçin</option>
                                @foreach ($districts as $d)
                                    <option value="{{ $d }}" @selected($district === $d)>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    @if ($districts->isEmpty())
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-sm text-amber-900">
                            Bu il için ilçe listesi boş; yine de aşağıdan hastaneleri il geneline göre görebilirsiniz. İlçe eklemek için hastane kayıtlarını güncelleyin.
                        </div>
                    @endif
                @endif

                @if ($city || !empty($nearMeActive))
                    <form method="get" action="{{ $ru([]) }}" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                        @if (!empty($nearMeActive))
                            <input type="hidden" name="near_lat" value="{{ $nearLat }}">
                            <input type="hidden" name="near_lng" value="{{ $nearLng }}">
                        @endif
                        @if ($city)
                            <input type="hidden" name="city" value="{{ $city }}">
                        @endif
                        @if (!empty($district))
                            <input type="hidden" name="district" value="{{ $district }}">
                        @endif
                        <div class="min-w-0 flex-1 sm:max-w-md">
                        <label for="hospital_q" class="mb-1 block text-sm font-medium text-slate-700">Kurum veya adres ara</label>
                            <input type="search" name="hospital_q" id="hospital_q" value="{{ $hospitalQ ?? '' }}" placeholder="Örn. şehir hastanesi, cadde…" autocomplete="off"
                                   class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                        </div>
                        <button type="submit" class="ui-focus-ring rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-600/20 hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-500/25 transition">
                            Ara
                        </button>
                    </form>
                    @if (!empty($nearMeActive) && $hospitals->isNotEmpty() && count($hospitalDistanceKm ?? []) === 0)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-950">
                            Hiçbir kurumda harita konumu tanımlı değil. Yönetim panelinden kurum kayıtlarına enlem/boylam girin veya il ile aramaya devam edin.
                        </div>
                    @endif
                    @if ($hospitals->isEmpty())
                        <div class="rounded-2xl border border-sky-200 bg-sky-50/50 px-4 py-3 text-sm text-slate-700">
                            @if (!empty($hospitalQ))
                                “{{ $hospitalQ }}” ile eşleşen kurum bulunamadı. Farklı kelime deneyin veya
                                <a href="{{ $ru(array_filter([
                                    'city' => $city,
                                    'district' => $district ?? null,
                                    'near_lat' => $nearMeActive ? $nearLat : null,
                                    'near_lng' => $nearMeActive ? $nearLng : null,
                                ])) }}" class="font-semibold text-sky-900 underline">aramayı temizleyin</a>.
                            @elseif (! $city && !empty($nearMeActive))
                                Filtrelere uyan aktif kurum yok.
                            @else
                                Bu il için kayıtlı aktif kurum yok (veya ilçe süzgecine uyan kurum yok). Farklı il veya ilçe deneyin ya da yönetimden kurum ekleyin.
                            @endif
                        </div>
                    @else
                        <div>
                            <p class="mb-2 text-sm font-medium text-slate-700">
                                Kurum seçin
                                @if (!empty($nearMeActive))
                                    <span class="font-normal text-slate-500">(yakınlık sırası)</span>
                                @endif
                            </p>
                            <ul class="flex flex-col gap-2" role="list">
                                @foreach ($hospitals as $h)
                                    @php
                                        $hospitalPickUrl = $ru(array_filter([
                                            'city' => $city,
                                            'district' => $district ?? null,
                                            'hospital_q' => $hospitalQ ?? null,
                                            'hospital_id' => $h->id,
                                            'near_lat' => $nearMeActive ? $nearLat : null,
                                            'near_lng' => $nearMeActive ? $nearLng : null,
                                        ]));
                                        $isPickedHospital = (int) ($hospitalId ?? 0) === (int) $h->id;
                                        $distKm = $hospitalDistanceKm[$h->id] ?? null;
                                    @endphp
                                    <li>
                                        <a href="{{ $hospitalPickUrl }}"
                                           class="flex w-full items-center justify-between gap-3 rounded-2xl border px-4 py-3 text-left text-sm font-semibold shadow-sm transition focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/20
                                                @if ($isPickedHospital) border-emerald-400 bg-gradient-to-br from-emerald-50 to-sky-50 text-emerald-950 ring-2 ring-emerald-500/30 @else border-sky-200 bg-white/80 text-slate-900 hover:border-emerald-300 hover:bg-emerald-50/40 @endif">
                                            <span class="min-w-0">{{ $h->name }}</span>
                                            <span class="flex shrink-0 flex-col items-end gap-1 sm:flex-row sm:items-center sm:gap-2">
                                                @if ($distKm !== null)
                                                    <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-bold tabular-nums text-sky-900">≈ {{ number_format($distKm, 1, ',', '.') }} km</span>
                                                @elseif (!empty($nearMeActive))
                                                    <span class="text-[10px] font-medium uppercase tracking-wide text-amber-800">Konum yok</span>
                                                @endif
                                                @if ($isPickedHospital)
                                                    <span class="rounded-full bg-emerald-600 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Seçili</span>
                                                @else
                                                    <span class="text-xs font-medium text-sky-600">Polikliniklere git →</span>
                                                @endif
                                            </span>
                                        </a>
                                        @if ($h->address)
                                            <p class="mt-1 px-1 text-xs text-slate-500">{{ $h->address }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
            </div>
        </section>
        @endunless

        <!-- Step 2: Department -->
        @if(! $aileHekimiOdak && !empty($hospitalId))
            <section class="rounded-2xl border border-sky-100/80 bg-white/75 hospital-glass p-4 shadow-sm surface-elevated ui-soft-rise">
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                        2
                    </span>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Birim seçimi</h2>
                </div>

                <form method="get" action="{{ $ru([]) }}" class="mb-3 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                    <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                    <div class="min-w-0 flex-1 sm:max-w-md">
                        <label for="department_q" class="mb-1 block text-sm font-medium text-slate-700">Poliklinik ara</label>
                        <input type="search" name="department_q" id="department_q" value="{{ $departmentQ ?? '' }}" placeholder="Birim adı…" autocomplete="off"
                               class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                    </div>
                    <button type="submit" class="ui-focus-ring rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-sky-600/20 hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-500/25 transition">
                        Listeyi süz
                    </button>
                </form>

                @if($departments->isNotEmpty())
                    <form method="get" action="{{ $ru([]) }}" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                        @if (!empty($departmentQ))
                            <input type="hidden" name="department_q" value="{{ $departmentQ }}">
                        @endif
                        <div class="min-w-[240px] flex-1">
                            <label for="department_id" class="mb-1 block text-sm font-medium text-slate-700">Poliklinik / Birim</label>
                            <select name="department_id" id="department_id" required class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                                <option value="">Seçin</option>
                                @foreach ($departments as $d)
                                    <option value="{{ $d->id }}" @selected((int) $departmentId === $d->id)>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                            Doktorları göster
                        </button>
                    </form>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-sm text-amber-900">
                        @if (!empty($departmentQ))
                            “{{ $departmentQ }}” aramasına uyan poliklinik bulunamadı. Farklı anahtar kelime deneyin veya <a href="{{ $ru(['hospital_id' => $hospitalId]) }}" class="font-semibold text-amber-950 underline">süzgeci temizleyin</a>.
                        @else
                            Bu kurumda henüz birime atanmış aktif doktor yok. Randevu için önce yönetim panelinden bu kuruma doktor ekleyin.
                        @endif
                    </div>
                @endif
            </section>
        @endif

        <!-- Step 3 & 4: Doctor + Date & Time -->
        @if(! $aileHekimiOdak && !empty($departmentId))
            @if($doctors->isNotEmpty() )
                <section class="rounded-2xl border border-sky-100/80 bg-white/75 hospital-glass p-4 shadow-sm surface-elevated ui-soft-rise">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                            3
                        </span>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Doktor seçimi</h2>
                    </div>

                    <form method="get" action="{{ $ru([]) }}" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                        <input type="hidden" name="department_id" value="{{ $departmentId }}">
                        @if (!empty($departmentQ))
                            <input type="hidden" name="department_q" value="{{ $departmentQ }}">
                        @endif
                        <div class="min-w-[240px] flex-1">
                            <label for="doctor_id" class="mb-1 block text-sm font-medium text-slate-700">Doktor</label>
                            <select name="doctor_id" id="doctor_id" required class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                                <option value="">Seçin</option>
                                @foreach ($doctors as $doc)
                                    @php
                                        $ad = $doc->user?->name ?? trim($doc->title.' — '.$doc->department?->name);
                                        $konum = trim(implode(' ', array_filter([
                                            $doc->physical_clinic_name,
                                            $doc->room_no ? '(Oda '.$doc->room_no.')' : null,
                                        ])));
                                    @endphp
                                    <option value="{{ $doc->id }}" @selected((int) $doctorId === $doc->id)>{{ $konum !== '' ? $ad.' — '.$konum : $ad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                            Devam — tarih ve saat
                        </button>
                    </form>
                </section>
                @elseif (!empty($departmentId))
                <div class="rounded-2xl border border-amber-200 bg-amber-50/70 hospital-glass p-4">
                    <div class="text-sm font-semibold text-amber-900">Bu birimde doktor bulunamadı.</div>
                    <div class="mt-1 text-sm text-amber-800">Bölümü değiştirip tekrar deneyin.</div>
                </div>
            @endif
        @endif

        @if(!empty($doctorId))
            @php
                $gizliRandevuModu = $gizliRandevuModu ?? false;
                $bookingExtra = $gizliRandevuModu ? ['gizli_randevu' => 1] : [];
            @endphp
            <section class="rounded-2xl border border-sky-100/80 bg-white/75 hospital-glass p-4 shadow-sm surface-elevated ui-soft-rise">
                <div class="mb-3 space-y-3">
                    @if($aileHekimiOdak && $aileHekimiDoctor)
                        <div class="rounded-2xl border border-violet-200/90 bg-gradient-to-br from-violet-50/90 to-emerald-50/40 px-3 py-2.5 text-sm text-slate-800">
                            <p class="font-bold text-violet-950">Aile hekiminiz</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $aileHekimiDoctor->user?->name ?? trim((string) $aileHekimiDoctor->title) ?: '—' }}</p>
                            @if($aileHekimiDoctor->title)
                                <p class="text-xs text-slate-600">{{ $aileHekimiDoctor->title }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $aileHekimiDoctor->hospital?->name ?? 'Kurum' }}</span>
                                @if($aileHekimiDoctor->department?->name)
                                    · {{ $aileHekimiDoctor->department->name }}
                                @endif
                            </p>
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                            {{ $aileHekimiOdak ? '1' : '4' }}
                        </span>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Tarih & Saat</h2>
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-slate-50/40 p-1.5">
                        <p class="mb-1.5 px-2 text-xs font-medium text-slate-600">Randevu türü</p>
                        <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                            <a href="{{ $ru(array_filter($ahMerge([
                                'hospital_id' => $hospitalId,
                                'department_id' => $departmentId,
                                'doctor_id' => $doctorId,
                                'randevu_date' => $selectedDateValue !== '' ? $selectedDateValue : null,
                            ]))) }}"
                               class="flex items-center justify-center rounded-xl px-3 py-2.5 text-center text-sm font-semibold transition
                                    @if(! $gizliRandevuModu) border-2 border-emerald-500 bg-white text-emerald-950 shadow-sm @else border border-transparent bg-white/60 text-slate-600 hover:bg-white @endif">
                                Standart randevu
                            </a>
                            <a href="{{ $ru(array_filter($ahMerge([
                                'hospital_id' => $hospitalId,
                                'department_id' => $departmentId,
                                'doctor_id' => $doctorId,
                                'randevu_date' => $selectedDateValue !== '' ? $selectedDateValue : null,
                                'gizli_randevu' => 1,
                            ]))) }}"
                               class="flex items-center justify-center rounded-xl px-3 py-2.5 text-center text-sm font-semibold transition
                                    @if($gizliRandevuModu) border-2 border-violet-600 bg-violet-50 text-violet-950 shadow-sm @else border border-transparent bg-white/60 text-slate-600 hover:bg-white @endif">
                                Gizli randevu
                            </a>
                        </div>
                    </div>

                    @if($oncelikliHasta ?? false)
                        <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/60 px-3 py-2 text-xs font-medium text-emerald-950">
                            Öncelikli hasta: blokların ilk saati size ayrılmış olabilir; diğer saatler de seçilebilir.
                        </div>
                    @endif

                    @if(!empty($availableDates) && $availableDates->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableDates->take(7) as $dStr)
                                <a href="{{ $ru(array_filter($ahMerge(array_merge([
                                    'hospital_id' => $hospitalId,
                                    'department_id' => $departmentId,
                                    'doctor_id' => $doctorId,
                                    'randevu_date' => $dStr,
                                ], $bookingExtra)))) }}"
                                   class="rounded-2xl border border-sky-200 bg-white/70 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-sky-50/60 transition
                                        @if($selectedDate && $selectedDate->toDateString() === $dStr) ring-4 ring-emerald-500/15 border-emerald-200 bg-gradient-to-br from-emerald-50 to-sky-50 text-emerald-900 @endif">
                                    {{ \Carbon\Carbon::parse($dStr)->format('d.m') }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <form method="get" action="{{ $ru([]) }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
                    @if($aileHekimiOdak)
                        <input type="hidden" name="aile_hekimi" value="1">
                    @endif
                    @if($gizliRandevuModu)
                        <input type="hidden" name="gizli_randevu" value="1">
                    @endif

                    <div class="min-w-[220px] flex-1">
                        <label for="randevu_date" class="mb-1 block text-sm font-medium text-slate-700">Tarih</label>
                        <input
                            type="date"
                            id="randevu_date"
                            name="randevu_date"
                            value="{{ $selectedDateValue }}"
                            @if($availableDates->isNotEmpty())
                                min="{{ $availableDates->first() }}"
                                max="{{ $availableDates->last() }}"
                            @endif
                            class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            onchange="this.form.submit()">
                    </div>

                    <button type="submit" class="ui-focus-ring hidden sm:inline-flex rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                        Saatleri göster
                    </button>
                </form>

                @if($availableDates->isEmpty())
                    <div class="mt-5 rounded-2xl border border-dashed border-amber-200 bg-amber-50/50 p-4 text-sm text-amber-900">
                        @if (! $doctorHasWorkingHours)
                            Bu doktor için haftalık çalışma saati tanımlı değil. Yönetim panelinden doktora çalışma saati ekledikten sonra müsait günler oluşturulur.
                        @else
                            Önümüzdeki {{ $bookingDaysAhead }} gün içinde gösterilebilecek boş randevu saati kalmadı (tümü dolu veya kapalı olabilir). @if($aileHekimiOdak) Daha sonra tekrar deneyin. @else Daha sonra tekrar deneyin veya başka bir doktor seçin. @endif
                        @endif
                    </div>
                @elseif(!$selectedDate)
                    <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white/60 p-4 text-sm text-slate-600">
                        Saatleri görmek için yukarıdan bir <strong class="font-semibold text-slate-800">tarih</strong> seçin veya üstteki gün kısayollarından birine tıklayın.
                    </div>
                @elseif($slots->isEmpty())
                    <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white/60 p-4 text-sm text-slate-600">
                        Seçilen tarihte müsait saat bulunamadı. Farklı tarih deneyin.
                    </div>
                @else
                    <form method="post" action="{{ route('musteri.randevu.kaydet') }}" class="mt-4 space-y-3">
                        @csrf
                        @if (! empty($proxyHastaId))
                            <input type="hidden" name="hasta_id" value="{{ $proxyHastaId }}">
                        @endif
                        @if($gizliRandevuModu)
                            <input type="hidden" name="gizli" value="1">
                        @endif

                        <fieldset @if($gizliRandevuModu) class="rounded-2xl border-2 border-violet-200/90 bg-violet-50/35 p-4 shadow-sm" @endif>
                            <legend class="sr-only">Müsait saatler</legend>
                            <div class="mb-2 text-sm font-semibold @if($gizliRandevuModu) text-violet-950 @else text-slate-900 @endif">
                                {{ $selectedDate?->format('d.m.Y') ?? $selectedDateValue }} — müsait saatler
                            </div>

                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                @foreach($slots as $slot)
                                    @php
                                        $oncelikliSlot = $slot->slot_tipi === \App\Enums\RandevuSlotTipi::Oncelikli;
                                    @endphp
                                    <div class="min-w-0">
                                        <label class="flex cursor-pointer">
                                            <input type="radio" name="randevu_slot_id" value="{{ $slot->id }}" required class="peer sr-only">
                                            <span class="flex w-full flex-col items-center justify-center gap-0.5 rounded-2xl border px-3 py-2.5 text-sm font-semibold transition
                                                @if($gizliRandevuModu)
                                                    border-violet-200 bg-white/80 text-violet-950 hover:border-violet-400 hover:shadow-sm peer-checked:border-violet-700 peer-checked:bg-gradient-to-br peer-checked:from-violet-50 peer-checked:to-fuchsia-50 peer-checked:ring-2 peer-checked:ring-violet-500/25
                                                @elseif($oncelikliSlot)
                                                    border-emerald-300/90 bg-emerald-50/50 text-emerald-950 hover:border-emerald-400 hover:shadow-sm peer-checked:border-emerald-600 peer-checked:bg-gradient-to-br peer-checked:from-emerald-50 peer-checked:to-teal-50
                                                @else
                                                    border-sky-200 bg-white/70 text-slate-900 hover:border-emerald-300 hover:shadow-sm peer-checked:border-emerald-600 peer-checked:bg-gradient-to-br peer-checked:from-emerald-50 peer-checked:to-sky-50 peer-checked:text-emerald-900
                                                @endif
                                                @if(! $gizliRandevuModu) peer-checked:ring-2 peer-checked:ring-emerald-500/20 @endif">
                                                <span class="tabular-nums">{{ $slot->baslangic->format('H:i') }} – {{ $slot->bitis->format('H:i') }}</span>
                                                @if($oncelikliSlot)
                                                    <span class="text-[10px] font-bold uppercase tracking-wide {{ $gizliRandevuModu ? 'text-violet-800' : 'text-emerald-800' }}">Öncelikli slot</span>
                                                @endif
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <div>
                            <label for="sikayet" class="mb-1 block text-sm font-medium text-slate-700">Şikâyet / not (isteğe bağlı)</label>
                            <textarea name="sikayet" id="sikayet" rows="2" maxlength="2000"
                                      class="w-full rounded-2xl border border-sky-200 bg-white/70 px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/50">{{ old('sikayet') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                                Randevuyu oluştur
                            </button>
                        </div>
                    </form>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('musteri.panel') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-900">Randevularıma dön</a>
                </div>

                @if (!empty($hospitalId) && !empty($departmentId))
                    @php
                        $isFavHospital = in_array((int) $hospitalId, array_map('intval', $favoriteHospitalIds ?? []), true);
                        $isFavClinic = false;
                        foreach ($favoriteClinicPairs ?? [] as $fc) {
                            if ((int) ($fc['hospital_id'] ?? 0) === (int) $hospitalId && (int) ($fc['department_id'] ?? 0) === (int) $departmentId) {
                                $isFavClinic = true;
                                break;
                            }
                        }
                    @endphp
                    <div class="mt-5 rounded-2xl border border-amber-200/90 bg-amber-50/25 hospital-glass p-4 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-amber-900">Favoriler</h3>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                            <form method="post" action="{{ route('musteri.favori.hastane.toggle') }}" class="inline">
                                @csrf
                                <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                                <button type="submit" class="ui-focus-ring rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ $isFavHospital ? 'border-amber-400 bg-amber-100/80 text-amber-950' : 'border-sky-200 bg-white text-slate-700 hover:bg-sky-50' }}">
                                    @if ($isFavHospital)
                                        ★ Hastane favorilerde — çıkar
                                    @else
                                        ☆ Bu hastaneyi favorilere ekle
                                    @endif
                                </button>
                            </form>
                            <form method="post" action="{{ route('musteri.favori.poliklinik.toggle') }}" class="inline">
                                @csrf
                                <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                                <button type="submit" class="ui-focus-ring rounded-2xl border px-3 py-2 text-xs font-semibold transition {{ $isFavClinic ? 'border-amber-400 bg-amber-100/80 text-amber-950' : 'border-emerald-200 bg-white text-emerald-900 hover:bg-emerald-50' }}">
                                    @if ($isFavClinic)
                                        ★ Poliklinik favorilerde — çıkar
                                    @else
                                        ☆ Bu polikliniği favorilere ekle
                                    @endif
                                </button>
                            </form>
                            <a href="{{ route('musteri.favoriler') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 sm:ml-auto">Tüm favorilerim →</a>
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('mhrs-near-me-btn');
            var st = document.getElementById('mhrs-near-me-status');
            if (!btn || !st) return;
            btn.addEventListener('click', function () {
                st.classList.remove('hidden');
                if (!navigator.geolocation) {
                    st.textContent = 'Bu cihaz veya tarayıcı konum paylaşımını desteklemiyor.';
                    return;
                }
                st.textContent = 'Konum isteniyor…';
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var base = btn.getAttribute('data-randevu-url') || window.location.pathname;
                        var u = new URL(base, window.location.origin);
                        var p = new URLSearchParams();
                        p.set('near_lat', String(pos.coords.latitude));
                        p.set('near_lng', String(pos.coords.longitude));
                        var cur = new URLSearchParams(window.location.search);
                        ['city', 'district', 'hospital_q', 'gizli_randevu'].forEach(function (k) {
                            if (cur.has(k)) {
                                p.set(k, cur.get(k));
                            }
                        });
                        u.search = p.toString();
                        window.location.href = u.toString();
                    },
                    function () {
                        st.textContent = 'Konum alınamadı. Tarayıcıda konum iznini açıp tekrar deneyin.';
                    },
                    { enableHighAccuracy: true, timeout: 20000, maximumAge: 60000 }
                );
            });
        })();
    </script>
@endsection


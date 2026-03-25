@extends('layouts.musteri')

@section('title', 'Randevu Al')

@section('content')
    @php
        $activeStep = 1;
        if (!empty($departmentId)) $activeStep = 2;
        if (!empty($doctorId)) $activeStep = 4; // Doktor seçildiğinde tarih-saat adımı görünür
        $selectedDateValue = $selectedDate?->format('Y-m-d') ?? now()->format('Y-m-d');
    @endphp

    <div class="flex flex-col gap-8 lg:grid lg:grid-cols-[minmax(0,1fr),minmax(260px,340px)] lg:items-start lg:gap-8">
        <aside class="order-1 lg:order-2 lg:col-start-2 lg:row-start-1 lg:min-w-0" aria-label="Görsel yardım ve ipuçları">
            <div class="lg:hidden mb-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Görsel yardım</p>
                <p class="mt-0.5 text-xs text-slate-500">Özet adımlar ve küçük çizimle randevu akışını hızlıca hatırlayın.</p>
            </div>
            <x-visual-help-panel sticky class="max-lg:shadow-lg" />
        </aside>

        <div class="order-2 min-w-0 space-y-6 lg:order-1 lg:col-start-1 lg:row-start-1">
    <div class="mb-0 rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="relative">
                <div class="absolute -left-1 top-0 h-9 w-1 rounded-full bg-gradient-to-b from-sky-500 to-emerald-500 opacity-90" aria-hidden="true"></div>
                <h1 class="pl-3 text-2xl font-extrabold tracking-tight text-sky-950">Randevu Al</h1>
                <p class="mt-2 max-w-xl pl-3 text-sm leading-relaxed text-slate-600">
                    Hastane → birim → doktor → tarih ve saat seçiminde adım adım ilerleyin; <span class="hidden lg:inline">sağdaki panelde</span><span class="lg:hidden">üstteki görsel yardımda</span> özet ipuçlarını görebilirsiniz.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 rounded-2xl border border-sky-100/90 bg-sky-50/40 p-3 sm:p-2 sm:bg-transparent sm:border-0 sm:p-0" role="list" aria-label="Randevu adımları">
                @php
                    $steps = [
                        [1, 'Hastane'],
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
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Step 1: Hospital -->
        <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                    1
                </span>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Hastane seçimi</h2>
            </div>

            @php
                $hospitals = [
                    ['id' => 1, 'name' => 'MHRS Merkez Hastanesi'],
                    ['id' => 2, 'name' => 'MHRS Şehir Hastanesi'],
                ];
            @endphp

            <form method="get" action="{{ route('musteri.randevu.al') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <label for="hospital_id" class="mb-1 block text-sm font-medium text-slate-700">Hastane</label>
                    <select name="hospital_id" id="hospital_id" class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                        <option value="">Seçin</option>
                        @foreach($hospitals as $h)
                            <option value="{{ $h['id'] }}" @selected((int)($hospitalId ?? 0) === (int)$h['id'])>{{ $h['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                    Birimlere geç
                </button>
            </form>
        </section>

        <!-- Step 2: Department -->
        @if(!empty($hospitalId) || !empty($departmentId))
            <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                        2
                    </span>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Birim seçimi</h2>
                </div>

                <form method="get" action="{{ route('musteri.randevu.al') }}" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
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
            </section>
        @endif

        <!-- Step 3 & 4: Doctor + Date & Time -->
        @if(!empty($departmentId))
            @if($doctors->isNotEmpty() )
                <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                            3
                        </span>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Doktor seçimi</h2>
                    </div>

                    <form method="get" action="{{ route('musteri.randevu.al') }}" class="flex flex-wrap items-end gap-4">
                        <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                        <input type="hidden" name="department_id" value="{{ $departmentId }}">
                        <div class="min-w-[240px] flex-1">
                            <label for="doctor_id" class="mb-1 block text-sm font-medium text-slate-700">Doktor</label>
                            <select name="doctor_id" id="doctor_id" required class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                                <option value="">Seçin</option>
                                @foreach ($doctors as $doc)
                                    @php
                                        $ad = $doc->user?->name ?? trim($doc->title.' — '.$doc->department?->name);
                                    @endphp
                                    <option value="{{ $doc->id }}" @selected((int) $doctorId === $doc->id)>{{ $ad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                            Tarih & saati seç
                        </button>
                    </form>
                </section>
            @elseif (!empty($departmentId))
                <div class="rounded-3xl border border-amber-200 bg-amber-50/70 hospital-glass p-5">
                    <div class="text-sm font-semibold text-amber-900">Bu birimde doktor bulunamadı.</div>
                    <div class="mt-1 text-sm text-amber-800">Bölümü değiştirip tekrar deneyin.</div>
                </div>
            @endif
        @endif

        @if(!empty($doctorId))
            <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50 text-sm font-extrabold text-sky-900">
                            4
                        </span>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Tarih & Saat</h2>
                    </div>

                    @if(!empty($availableDates) && $availableDates->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableDates->take(7) as $dStr)
                                <a href="{{ route('musteri.randevu.al', [
                                    'hospital_id' => $hospitalId,
                                    'department_id' => $departmentId,
                                    'doctor_id' => $doctorId,
                                    'randevu_date' => $dStr,
                                ]) }}"
                                   class="rounded-2xl border border-sky-200 bg-white/70 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-sky-50/60 transition
                                        @if($selectedDate && $selectedDate->toDateString() === $dStr) ring-4 ring-emerald-500/15 border-emerald-200 bg-gradient-to-br from-emerald-50 to-sky-50 text-emerald-900 @endif">
                                    {{ \Carbon\Carbon::parse($dStr)->format('d.m') }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <form method="get" action="{{ route('musteri.randevu.al') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <input type="hidden" name="hospital_id" value="{{ $hospitalId }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="doctor_id" value="{{ $doctorId }}">

                    <div class="min-w-[220px] flex-1">
                        <label for="randevu_date" class="mb-1 block text-sm font-medium text-slate-700">Tarih</label>
                        <input
                            type="date"
                            id="randevu_date"
                            name="randevu_date"
                            value="{{ $selectedDateValue }}"
                            class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            onchange="this.form.submit()">
                    </div>

                    <button type="submit" class="ui-focus-ring hidden sm:inline-flex rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                        Saatleri güncelle
                    </button>
                </form>

                @if($slots->isEmpty())
                    <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white/60 p-4 text-sm text-slate-600">
                        Seçilen tarihte müsait saat bulunamadı. Farklı tarih deneyin.
                    </div>
                @else
                    <form method="post" action="{{ route('musteri.randevu.kaydet') }}" class="mt-6 space-y-5">
                        @csrf

                        <fieldset>
                            <legend class="sr-only">Müsait saatler</legend>
                            <div class="mb-3 text-sm font-semibold text-slate-900">
                                {{ $selectedDate?->format('d.m.Y') ?? $selectedDateValue }} için müsait saatler
                            </div>

                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($slots as $slot)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="randevu_slot_id" value="{{ $slot->id }}" required class="peer sr-only">
                                        <span class="flex items-center justify-center rounded-2xl border border-sky-200 bg-white/70 px-3 py-2 text-sm font-semibold text-slate-900 transition
                                            hover:border-emerald-300 hover:shadow-sm peer-checked:border-emerald-600 peer-checked:bg-gradient-to-br peer-checked:from-emerald-50 peer-checked:to-sky-50 peer-checked:text-emerald-900">
                                            {{ $slot->baslangic->format('H:i') }} - {{ $slot->bitis->format('H:i') }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div>
                            <label for="sikayet" class="mb-1 block text-sm font-medium text-slate-700">Şikâyet / not (isteğe bağlı)</label>
                            <textarea name="sikayet" id="sikayet" rows="3" maxlength="2000"
                                      class="w-full rounded-2xl border border-sky-200 bg-white/70 px-3 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/50">{{ old('sikayet') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-xs text-slate-500">
                                Randevu oluşturulduğunda slot otomatik rezerve edilecektir.
                            </div>
                            <button type="submit" class="ui-focus-ring rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                                Randevuyu oluştur
                            </button>
                        </div>
                    </form>
                @endif

                <div class="mt-6 text-center">
                    <a href="{{ route('musteri.panel') }}" class="font-medium text-emerald-700 hover:text-emerald-900">Randevularıma dön</a>
                </div>
            </section>
        @endif
    </div>
        </div>
    </div>
@endsection


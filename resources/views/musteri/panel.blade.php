@extends('layouts.musteri')

@section('title', 'Hasta Paneli')

@section('content')
    @php
        $tumYaklasan = $yaklasanRandevularAcik->count() + $yaklasanRandevularGizli->count();
        $tumSon = $sonRandevular->count();
        $favoriToplam = $favoriteHospitalsPreview->count() + $favoriteClinicsPreview->count();
        $ilkYaklasan = $yaklasanRandevularAcik->first() ?? $yaklasanRandevularGizli->first();
    @endphp

    <div class="mb-6 overflow-hidden rounded-3xl border border-sky-100/90 bg-gradient-to-br from-white/90 via-sky-50/50 to-emerald-50/40 p-5 shadow-sm surface-elevated sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/90 shadow-sm sm:flex" aria-hidden="true">
                    <svg class="h-8 w-8" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 8L38 16V32C38 38 32 42 24 44C16 42 10 38 10 32V16L24 8Z" stroke="url(#ph)" stroke-width="2.2" stroke-linejoin="round"/>
                        <path d="M18 24L22 28L30 20" stroke="url(#ph)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="ph" x1="10" y1="8" x2="38" y2="44" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0ea5e9"/><stop offset="1" stop-color="#10b981"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-2xl font-extrabold tracking-tight text-sky-950">Hasta paneli</h1>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">Yaklaşan randevularınız ve son kayıtlarınız tek bakışta.</p>
                    <ul class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold text-slate-600" aria-label="Hızlı ipuçları">
                        <li class="rounded-full border border-sky-200/80 bg-white/70 px-2.5 py-1">Randevu al: adım adım</li>
                        <li class="rounded-full border border-emerald-200/80 bg-white/70 px-2.5 py-1">İptal: kart üzerinden</li>
                        <li class="rounded-full border border-amber-200/80 bg-white/70 px-2.5 py-1">
                            <a href="{{ route('musteri.favoriler') }}" class="text-amber-900 hover:underline">Favoriler</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:items-end">
                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition sm:w-auto">
                    Yeni randevu al
                </a>
                <a href="{{ route('musteri.randevu.al', ['kurum_tipi' => 'saglik_merkezi']) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-teal-200/90 bg-white/90 px-4 py-2 text-sm font-semibold text-teal-900 shadow-sm transition hover:border-teal-300 hover:bg-teal-50/80 focus:outline-none focus:ring-4 focus:ring-teal-500/20 sm:w-auto">
                    <svg class="h-4 w-4 shrink-0 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Sağlık merkezinden randevu
                </a>
            </div>
        </div>
    </div>

    <section class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="group rounded-3xl border border-emerald-100/80 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Yaklaşan toplam</p>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">🗓️</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $tumYaklasan }}</p>
            <p class="mt-1 text-xs text-slate-500">Onaylı ve bekleyen randevular</p>
        </article>

        <article class="group rounded-3xl border border-violet-100/80 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-wide text-violet-700">Gizli randevu</p>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">🔒</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $yaklasanRandevularGizli->count() }}</p>
            <p class="mt-1 text-xs text-slate-500">Özel görünürlükte kayıtlar</p>
        </article>

        <article class="group rounded-3xl border border-amber-100/80 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Favorilerim</p>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">★</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $favoriToplam }}</p>
            <p class="mt-1 text-xs text-slate-500">Kayıtlı kurum ve poliklinik</p>
        </article>

        <article class="group rounded-3xl border border-sky-100/80 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-700">Son kayıtlar</p>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">📌</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900">{{ $tumSon }}</p>
            <p class="mt-1 text-xs text-slate-500">Panelde görünen son randevu</p>
        </article>
    </section>

    <section class="mb-6 rounded-3xl border border-sky-100/80 bg-gradient-to-r from-sky-50/60 via-white/85 to-emerald-50/40 p-5 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Akıllı hızlı işlemler</h2>
                <p class="mt-1 text-xs text-slate-600">
                    @if ($ilkYaklasan && $ilkYaklasan->slot)
                        Sıradaki randevunuz: <span class="font-semibold text-slate-800">{{ $ilkYaklasan->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}</span>
                    @else
                        Henüz yaklaşan randevu görünmüyor; hemen yeni bir randevu planlayabilirsiniz.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center rounded-2xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-emerald-600/25 hover:bg-emerald-700 transition">
                    Randevu al
                </a>
                <a href="{{ route('musteri.randevu.gecmis') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-white/80 px-3.5 py-2 text-xs font-semibold text-sky-800 hover:bg-sky-50 transition">
                    Geçmişi görüntüle
                </a>
                <a href="{{ route('musteri.favoriler') }}" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white/80 px-3.5 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-50 transition">
                    Favorileri yönet
                </a>
            </div>
        </div>
    </section>

    @if ($favoriteHospitalsPreview->isNotEmpty() || $favoriteClinicsPreview->isNotEmpty())
        <section class="mb-6 rounded-3xl border border-amber-100/90 bg-gradient-to-br from-amber-50/60 via-white/80 to-emerald-50/40 hospital-glass p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-amber-950">Favorilerim</h2>
                    <p class="mt-0.5 text-xs text-slate-600">Kayıtlı hastane ve polikliniklere hızlı randevu</p>
                </div>
                <a href="{{ route('musteri.favoriler') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">Tümünü yönet →</a>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($favoriteHospitalsPreview as $h)
                    <a href="{{ route('musteri.randevu.al', ['hospital_id' => $h->id]) }}"
                       class="inline-flex max-w-full items-center gap-1.5 rounded-2xl border border-amber-200/90 bg-white/80 px-3 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-amber-50/80 transition">
                        <span class="text-amber-600" aria-hidden="true">★</span>
                        <span class="truncate">{{ $h->name }}</span>
                    </a>
                @endforeach
                @foreach ($favoriteClinicsPreview as $row)
                    @php
                        $fh = $row['hospital'];
                        $fd = $row['department'];
                    @endphp
                    <a href="{{ route('musteri.randevu.al', ['hospital_id' => $fh->id, 'department_id' => $fd->id]) }}"
                       class="inline-flex max-w-full flex-col rounded-2xl border border-emerald-200/90 bg-white/80 px-3 py-2 text-left text-xs font-semibold text-slate-800 shadow-sm hover:bg-emerald-50/60 transition">
                        <span class="truncate text-emerald-900">{{ $fd->name }}</span>
                        <span class="truncate text-[10px] font-medium text-slate-500">{{ $fh->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="space-y-4">
            <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Yaklaşan randevular</div>
                        <div class="mt-0.5 text-xs text-slate-600">Standart — onaylı ve gelecek tarihli</div>
                    </div>
                    <div class="inline-flex items-center rounded-2xl border border-emerald-200/70 bg-emerald-50/70 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ $yaklasanRandevularAcik->count() }}
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @if ($yaklasanRandevularAcik->isEmpty())
                        <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 p-4 text-sm text-slate-600">
                            Standart yaklaşan randevunuz yok.
                        </div>
                    @else
                        @foreach ($yaklasanRandevularAcik as $r)
                            <div class="rounded-2xl border border-sky-100 bg-white/60 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">
                                            @if ($r->slot)
                                                {{ $r->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-slate-600">
                                            Dr. {{ $r->doctor?->user?->name ?? $r->doctor?->title }}
                                            · {{ $r->doctor?->department?->name ?? 'Birim' }}
                                        </div>
                                    </div>
                                    @php
                                        $yEtiket = match ($r->durum) {
                                            \App\Enums\RandevuDurumu::Onaylandi => ['Onaylandı', 'bg-emerald-100 text-emerald-900'],
                                            default => ['Bekliyor', 'bg-amber-100 text-amber-900'],
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $yEtiket[1] }}">
                                        {{ $yEtiket[0] }}
                                    </span>
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <div class="text-[11px] text-slate-500">
                                        {{ $r->sikayet ? 'Not: ' . \Illuminate\Support\Str::limit($r->sikayet, 70) : 'Şikayet yok' }}
                                    </div>
                                    <form method="post" action="{{ route('musteri.randevu.iptal', $r) }}" class="inline" onsubmit="return confirm('Randevuyu iptal etmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50/60 transition">
                                            İptal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>

            <section class="rounded-3xl border border-violet-200/80 bg-violet-50/40 hospital-glass p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-violet-950">Gizli yaklaşan randevular</div>
                        <div class="mt-0.5 text-xs text-violet-800/80">Gizli randevu aldığınız kayıtlar burada listelenir</div>
                    </div>
                    <div class="inline-flex items-center rounded-2xl border border-violet-300/70 bg-white/80 px-3 py-1 text-xs font-bold text-violet-800">
                        {{ $yaklasanRandevularGizli->count() }}
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @if ($yaklasanRandevularGizli->isEmpty())
                        <div class="rounded-2xl border border-dashed border-violet-200 bg-white/50 p-4 text-sm text-violet-900/80">
                            Gizli randevunuz yok. Randevu alırken <strong class="font-semibold">Gizli randevu</strong> seçeneğini kullanabilirsiniz.
                        </div>
                    @else
                        @foreach ($yaklasanRandevularGizli as $r)
                            <div class="rounded-2xl border border-violet-200/90 bg-white/70 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-violet-950">
                                            @if ($r->slot)
                                                {{ $r->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-violet-900/80">
                                            Dr. {{ $r->doctor?->user?->name ?? $r->doctor?->title }}
                                            · {{ $r->doctor?->department?->name ?? 'Birim' }}
                                        </div>
                                    </div>
                                    <span class="inline-flex rounded-full bg-violet-200/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-violet-950">Gizli</span>
                                </div>
                                @php
                                    $yEtiketG = match ($r->durum) {
                                        \App\Enums\RandevuDurumu::Onaylandi => ['Onaylandı', 'bg-emerald-100 text-emerald-900'],
                                        default => ['Bekliyor', 'bg-amber-100 text-amber-900'],
                                    };
                                @endphp
                                <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $yEtiketG[1] }}">{{ $yEtiketG[0] }}</span>
                                    <form method="post" action="{{ route('musteri.randevu.iptal', $r) }}" class="inline" onsubmit="return confirm('Randevuyu iptal etmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50/60 transition">
                                            İptal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
        </div>

        <section class="panel-inpage-sticky rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Son Randevular</div>
                    <div class="mt-0.5 text-xs text-slate-600">En son {{ $sonRandevular->count() }} kayıt</div>
                </div>
                <a href="{{ route('musteri.randevu.gecmis') }}" class="shrink-0 text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                    Geçmiş randevular →
                </a>
            </div>

            <div class="mt-4 space-y-2">
                @forelse ($sonRandevular as $r)
                    @php
                        $durumEtiket = match ($r->durum) {
                            \App\Enums\RandevuDurumu::Bekliyor => ['Bekliyor', 'bg-amber-100 text-amber-900'],
                            \App\Enums\RandevuDurumu::Onaylandi => ['Onaylandı', 'bg-teal-100 text-teal-900'],
                            \App\Enums\RandevuDurumu::Tamamlandi => ['Tamamlandı', 'bg-emerald-100 text-emerald-900'],
                            \App\Enums\RandevuDurumu::Iptal => ['İptal', 'bg-sky-200 text-sky-950'],
                            \App\Enums\RandevuDurumu::Gelmedi => ['Gelmedi', 'bg-rose-100 text-rose-900'],
                        };
                    @endphp
                    <div class="flex items-center gap-3 rounded-2xl border border-sky-100 bg-white/60 px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900">
                                @if ($r->slot)
                                    {{ $r->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                @else
                                    —
                                @endif
                            </div>
                            <div class="truncate text-[11px] text-slate-500">
                                {{ $r->doctor?->user?->name ?? $r->doctor?->title ?? '—' }}
                                @if ($r->doctor?->department) · {{ $r->doctor->department->name }} @endif
                                @if ($r->gizli)
                                    <span class="ml-1 inline-flex rounded-full bg-violet-100 px-1.5 py-0.5 text-[9px] font-bold text-violet-900">Gizli</span>
                                @endif
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $durumEtiket[1] }}">{{ $durumEtiket[0] }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 p-6 text-center text-sm text-slate-600">
                        Henüz randevunuz yok.
                        <a href="{{ route('musteri.randevu.al') }}" class="mt-2 block font-medium text-emerald-700 hover:text-emerald-900">Randevu alın</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection

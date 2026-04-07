@extends('layouts.admin')

@section('title', 'Yönetim Merkezi')
@section('subtitle', 'Bugün ve bekleyen randevular')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-sky-100/80 bg-gradient-to-r from-sky-600 via-cyan-600 to-emerald-600 p-6 text-white shadow-lg">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.35),transparent_50%)]"></div>
        <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-wide">
                    Yönetim Merkezi
                </div>
                <h2 class="mt-3 text-2xl font-black leading-tight lg:text-3xl">Bugünkü operasyon görünümü</h2>
                <p class="mt-1 max-w-2xl text-sm text-white/90">Randevular, doktor yoğunluğu ve kurum kapasitesi tek ekranda.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:max-w-xs">
                <a href="{{ route('admin.randevular.index') }}" class="rounded-2xl border border-white/30 bg-white/15 px-4 py-3 text-left backdrop-blur transition hover:bg-white/25">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-white/85">Bugün</div>
                    <div class="mt-1 text-2xl font-black">{{ $todayCount }}</div>
                </a>
                <a href="{{ route('admin.randevular.index', ['durum' => 'bekliyor']) }}" class="rounded-2xl border border-white/30 bg-white/15 px-4 py-3 text-left backdrop-blur transition hover:bg-white/25">
                    <div class="text-[11px] font-semibold uppercase tracking-wide text-white/85">Bekleyen</div>
                    <div class="mt-1 text-2xl font-black">{{ $bekleyenToplam }}</div>
                </a>
            </div>
        </div>
    </section>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-sky-100/80 bg-white/75 p-4 hospital-glass shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wide text-sky-600">Aktif doktor</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $doktorSayisi }}</div>
            <div class="mt-1 text-xs text-slate-500">Sistemde hizmet veren hekim</div>
        </div>
        <div class="rounded-3xl border border-emerald-100/90 bg-white/75 p-4 hospital-glass shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">Aktif kurum</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $hastaneSayisi }}</div>
            <div class="mt-1 text-xs text-slate-500">Hastane ve sağlık merkezi</div>
        </div>
        <div class="rounded-3xl border border-teal-100/90 bg-white/75 p-4 hospital-glass shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wide text-teal-700">Tamamlanan</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $tamamlananBugun }}</div>
            <div class="mt-1 text-xs text-slate-500">Bugün sonuçlanan randevu</div>
        </div>
        <div class="rounded-3xl border border-indigo-100/90 bg-white/75 p-4 hospital-glass shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wide text-indigo-700">Tamamlanma oranı</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">%{{ $dolulukOraniBugun }}</div>
            <div class="mt-1 h-2 overflow-hidden rounded-full bg-indigo-100">
                <div class="h-full rounded-full bg-indigo-500" style="width: {{ max(0, min(100, $dolulukOraniBugun)) }}%"></div>
            </div>
        </div>
    </div>

    <section class="mt-5 grid gap-4 xl:grid-cols-3">
        <div class="rounded-3xl border border-sky-100/80 bg-white/75 p-5 hospital-glass shadow-sm xl:col-span-2">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <div class="text-sm font-bold text-slate-900">Bugünün programı</div>
                    <div class="mt-0.5 text-xs text-slate-500">Saate göre sıralı canlı görünüm (en fazla 24 kayıt)</div>
                </div>
                <a href="{{ route('admin.randevular.index', ['durum' => 'aktif']) }}" class="shrink-0 text-xs font-bold text-sky-700 hover:text-sky-900">Tümü →</a>
            </div>
            <div class="mt-4 space-y-2.5 max-h-[min(29rem,62vh)] overflow-y-auto pr-1">
                @forelse($todayTimeline as $a)
                    @php
                        $durumRaw = $a->getRawOriginal('durum') ?? 'bekliyor';
                        $badge = match ($durumRaw) {
                            'bekliyor' => 'bg-amber-100 text-amber-900',
                            'onaylandi' => 'bg-teal-100 text-teal-900',
                            'tamamlandi' => 'bg-emerald-100 text-emerald-900',
                            'iptal' => 'bg-sky-200 text-sky-950',
                            'gelmedi' => 'bg-rose-100 text-rose-900',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        $durumLabel = match ($durumRaw) {
                            'bekliyor' => 'Bekliyor',
                            'onaylandi' => 'Onaylandı',
                            'tamamlandi' => 'Tamamlandı',
                            'iptal' => 'İptal',
                            'gelmedi' => 'Gelmedi',
                            default => $durumRaw,
                        };
                    @endphp
                    <div class="group flex gap-3 rounded-2xl border border-sky-100/80 bg-white/80 px-3 py-3 transition hover:border-sky-200 hover:shadow-sm">
                        <div class="w-14 shrink-0 text-center">
                            <div class="text-xs font-extrabold text-slate-900">
                                @if($a->slot?->baslangic)
                                    {{ $a->slot->baslangic->timezone(config('app.timezone'))->format('H:i') }}
                                @else
                                    —
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400">saat</div>
                        </div>
                        <div class="my-1 w-px shrink-0 bg-sky-100"></div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-slate-900">{{ $a->user?->name ?? 'Hasta' }}</div>
                            <div class="truncate text-[11px] text-slate-500">{{ $a->doctor?->user?->name ?? $a->doctor?->title ?? 'Doktor' }} @if($a->doctor?->department) · {{ $a->doctor->department->name }} @endif</div>
                        </div>
                        <span class="shrink-0 self-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $badge }}">{{ $durumLabel }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-sky-200 bg-white/50 py-10 text-center text-sm text-slate-500">
                        Bugün için slotu olan randevu yok.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <section class="rounded-3xl border border-sky-100/80 bg-white/75 p-5 hospital-glass shadow-sm">
                <div class="text-sm font-bold text-slate-900">Hızlı işlemler</div>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('admin.randevular.index') }}" class="flex items-center justify-between rounded-2xl border border-sky-100 bg-sky-50/40 px-3 py-2.5 text-sm font-semibold text-sky-900 transition hover:bg-sky-100/60">
                        <span>Randevu listesi</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('admin.doktorlar.index') }}" class="flex items-center justify-between rounded-2xl border border-emerald-100 bg-emerald-50/40 px-3 py-2.5 text-sm font-semibold text-emerald-900 transition hover:bg-emerald-100/60">
                        <span>Doktorları yönet</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('admin.hastaneler.index') }}" class="flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/40 px-3 py-2.5 text-sm font-semibold text-indigo-900 transition hover:bg-indigo-100/60">
                        <span>Hastane yönetimi</span>
                        <span>→</span>
                    </a>
                </div>
            </section>

            <section class="rounded-3xl border border-sky-100/80 bg-white/75 p-5 hospital-glass shadow-sm">
                <div class="text-sm font-bold text-slate-900">Sistem notu</div>
                <p class="mt-2 text-xs leading-relaxed text-slate-600">
                    Bu ekran, günlük operasyon yükünü hızla takip etmeniz için optimize edildi.
                    Bekleyen randevular yükselirse randevu listesine giderek filtreleyebilir ve müdahale edebilirsiniz.
                </p>
            </section>
        </div>
    </section>
@endsection

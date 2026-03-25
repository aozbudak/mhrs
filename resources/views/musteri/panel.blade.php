@extends('layouts.musteri')

@section('title', 'Hasta Paneli')

@section('content')
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
                    </ul>
                </div>
            </div>
            <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25 transition">
                Yeni randevu al
            </a>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Yaklaşan Randevular</div>
                    <div class="mt-0.5 text-xs text-slate-600">Onaylı ve gelecek tarihli</div>
                </div>
                <div class="inline-flex items-center rounded-2xl border border-emerald-200/70 bg-emerald-50/70 px-3 py-1 text-xs font-bold text-emerald-700">
                    {{ $yaklasanRandevular?->count() ?? 0 }}
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @if ($yaklasanRandevular->isEmpty())
                    <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 p-4 text-sm text-slate-600">
                        Yaklaşan randevunuz bulunmuyor.
                    </div>
                @else
                    @foreach ($yaklasanRandevular as $r)
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
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-900">
                                    Bekliyor
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

        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
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

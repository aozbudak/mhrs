@extends('layouts.musteri')

@section('title', 'Geçmiş Randevular')

@section('content')
    <div class="mb-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-gradient-to-br from-white/95 via-sky-50/40 to-emerald-50/30 p-5 shadow-md surface-elevated sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/95 shadow-sm sm:flex" aria-hidden="true">
                    <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Kayıt arşivi</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-sky-950">Geçmiş randevular</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">Tamamlanan, iptal edilen veya tarihi geçmiş kayıtlarınız tek listede.</p>
                </div>
            </div>
            <a href="{{ route('musteri.panel') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200/80 bg-white/85 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-900">
                Panele dön
            </a>
        </div>
    </div>

    <section class="rounded-3xl border border-sky-200/50 bg-white/75 hospital-glass p-5 shadow-lg shadow-sky-900/[0.04]">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3 border-b border-sky-100/80 pb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Tüm kayıtlar</h2>
                <p class="mt-0.5 text-xs text-slate-500">Durum rozetleriyle hızlıca ayırt edin</p>
            </div>
        </div>
        <div class="space-y-3">
            @forelse ($gecmisRandevular as $r)
                @php
                    $durumEtiket = match ($r->durum) {
                        \App\Enums\RandevuDurumu::Bekliyor => ['Bekliyor (tarih geçti)', 'bg-amber-100 text-amber-900'],
                        \App\Enums\RandevuDurumu::Tamamlandi => ['Tamamlandı', 'bg-emerald-100 text-emerald-900'],
                        \App\Enums\RandevuDurumu::Iptal => ['İptal', 'bg-sky-200 text-sky-950'],
                        \App\Enums\RandevuDurumu::Gelmedi => ['Gelmedi', 'bg-rose-100 text-rose-900'],
                    };
                @endphp
                <div class="rounded-2xl border border-sky-100/90 bg-white/80 p-4 shadow-sm transition hover:border-sky-200/90 hover:shadow-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900">
                                @if ($r->slot)
                                    {{ $r->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                @else
                                    —
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-slate-600">
                                Dr. {{ $r->doctor?->user?->name ?? $r->doctor?->title ?? '—' }}
                                @if ($r->doctor?->department) · {{ $r->doctor->department->name }} @endif
                            </div>
                            @if ($r->sikayet)
                                <div class="mt-2 text-[11px] text-slate-500">
                                    Not: {{ \Illuminate\Support\Str::limit($r->sikayet, 160) }}
                                </div>
                            @endif
                        </div>
                        <span class="shrink-0 self-start rounded-full px-2.5 py-1 text-[10px] font-bold {{ $durumEtiket[1] }}">{{ $durumEtiket[0] }}</span>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 p-10 text-center text-sm text-slate-600">
                    Geçmiş randevu kaydınız bulunmuyor.
                    <a href="{{ route('musteri.randevu.al') }}" class="mt-2 block font-medium text-emerald-700 hover:text-emerald-900">Randevu alın</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection

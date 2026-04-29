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
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                        @if (! empty($gecmisVekilModu) && isset($gecmisGorunenHasta))
                            <strong class="font-semibold text-slate-800">{{ $gecmisGorunenHasta->name }}</strong> adına görüntüleniyor; tamamlanan, iptal edilen veya tarihi geçmiş kayıtlar.
                        @else
                            Tamamlanan, iptal edilen veya tarihi geçmiş kayıtlarınız tek listede.
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:items-end">
                @if (! empty($gecmisVekilModu))
                    <a href="{{ route('musteri.randevu.gecmis') }}" class="inline-flex items-center justify-center rounded-2xl border border-indigo-200 bg-indigo-50/80 px-5 py-2.5 text-sm font-semibold text-indigo-950 shadow-sm transition hover:bg-indigo-100">
                        Kendi geçmişime dön
                    </a>
                @endif
                <a href="{{ route('musteri.panel') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200/80 bg-white/85 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-900">
                    Panele dön
                </a>
            </div>
        </div>
    </div>

    <section class="rounded-3xl border border-sky-200/50 bg-white/75 hospital-glass p-5 shadow-lg shadow-sky-900/[0.04]">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3 border-b border-sky-100/80 pb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Tüm kayıtlar</h2>
                <p class="mt-0.5 text-xs text-slate-500">Durum rozetleriyle hızlıca ayırt edin</p>
            </div>
        </div>
        <div class="space-y-8">
            @if ($gecmisRandevularGizli->isEmpty() && $gecmisRandevularAcik->isEmpty())
                <div class="rounded-2xl border border-dashed border-sky-200 bg-white/60 p-10 text-center text-sm text-slate-600">
                    @if (! empty($gecmisVekilModu) && isset($gecmisGorunenHasta))
                        Bu hasta için geçmiş randevu kaydı bulunmuyor.
                        <a href="{{ route('musteri.randevu.al', ['hasta_id' => $gecmisGorunenHasta->id]) }}" class="mt-2 block font-medium text-emerald-700 hover:text-emerald-900">Randevu alın</a>
                    @else
                        Geçmiş randevu kaydınız bulunmuyor.
                        <a href="{{ route('musteri.randevu.al') }}" class="mt-2 block font-medium text-emerald-700 hover:text-emerald-900">Randevu alın</a>
                    @endif
                </div>
            @else
                @if ($gecmisRandevularGizli->isNotEmpty())
                    <div>
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-violet-800">Gizli randevular</h3>
                        <div class="space-y-3">
                            @foreach ($gecmisRandevularGizli as $r)
                                @php
                                    $durumEtiket = match ($r->durum) {
                                        \App\Enums\RandevuDurumu::Bekliyor => ['Bekliyor (tarih geçti)', 'bg-amber-100 text-amber-900'],
                                        \App\Enums\RandevuDurumu::Onaylandi => ['Onaylandı (tarih geçti)', 'bg-teal-100 text-teal-900'],
                                        \App\Enums\RandevuDurumu::Tamamlandi => ['Tamamlandı', 'bg-emerald-100 text-emerald-900'],
                                        \App\Enums\RandevuDurumu::Iptal => ['İptal', 'bg-sky-200 text-sky-950'],
                                        \App\Enums\RandevuDurumu::Gelmedi => ['Gelmedi', 'bg-rose-100 text-rose-900'],
                                    };
                                @endphp
                                @include('musteri.partials.randevu-kart', [
                                    'r' => $r,
                                    'durumEtiket' => $durumEtiket,
                                    'cardClass' => 'rounded-2xl border border-violet-200/90 bg-violet-50/30 p-4 shadow-sm transition hover:border-violet-300/90 hover:shadow-md',
                                    'titleClass' => 'text-sm font-semibold text-violet-950',
                                    'subClass' => 'mt-1 text-xs text-violet-900/85',
                                    'notClass' => 'text-violet-900/70',
                                    'gizliRozet' => true,
                                    'haritaModu' => 'full',
                                ])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($gecmisRandevularAcik->isNotEmpty())
                    <div>
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Diğer geçmiş randevular</h3>
                        <div class="space-y-3">
                            @foreach ($gecmisRandevularAcik as $r)
                                @php
                                    $durumEtiket = match ($r->durum) {
                                        \App\Enums\RandevuDurumu::Bekliyor => ['Bekliyor (tarih geçti)', 'bg-amber-100 text-amber-900'],
                                        \App\Enums\RandevuDurumu::Onaylandi => ['Onaylandı (tarih geçti)', 'bg-teal-100 text-teal-900'],
                                        \App\Enums\RandevuDurumu::Tamamlandi => ['Tamamlandı', 'bg-emerald-100 text-emerald-900'],
                                        \App\Enums\RandevuDurumu::Iptal => ['İptal', 'bg-sky-200 text-sky-950'],
                                        \App\Enums\RandevuDurumu::Gelmedi => ['Gelmedi', 'bg-rose-100 text-rose-900'],
                                    };
                                @endphp
                                @include('musteri.partials.randevu-kart', [
                                    'r' => $r,
                                    'durumEtiket' => $durumEtiket,
                                    'cardClass' => 'rounded-2xl border border-sky-100/90 bg-white/80 p-4 shadow-sm transition hover:border-sky-200/90 hover:shadow-md',
                                    'haritaModu' => 'full',
                                ])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection

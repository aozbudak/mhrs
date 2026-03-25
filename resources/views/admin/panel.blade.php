@extends('layouts.admin')

@section('title', 'Özet')
@section('subtitle', 'Bugün ve bekleyen randevular')

@section('content')
    <div class="grid gap-3 sm:grid-cols-2">
        <a href="{{ route('admin.randevular.index') }}" class="group rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-4 shadow-sm transition hover:border-sky-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="text-xs font-bold uppercase tracking-wide text-sky-600">Bugün</div>
                <span class="rounded-lg bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 opacity-0 transition group-hover:opacity-100">Liste →</span>
            </div>
            <div class="mt-1 text-2xl font-extrabold text-emerald-700">{{ $todayCount }}</div>
            <div class="mt-1 text-[11px] leading-snug text-slate-500">İptal hariç, slotu bugün olan</div>
        </a>
        <a href="{{ route('admin.randevular.index', ['durum' => 'bekliyor']) }}" class="group rounded-3xl border border-amber-100/90 bg-amber-50/40 hospital-glass p-4 shadow-sm transition hover:border-amber-200 hover:shadow-md">
            <div class="flex items-start justify-between gap-2">
                <div class="text-xs font-bold uppercase tracking-wide text-amber-800">Bekleyen</div>
                <span class="text-[10px] font-bold text-amber-900 opacity-0 group-hover:opacity-100">Filtrele →</span>
            </div>
            <div class="mt-1 text-2xl font-extrabold text-amber-800">{{ $bekleyenToplam }}</div>
            <div class="mt-1 text-[11px] leading-snug text-slate-600">Tüm zamanlar</div>
        </a>
    </div>

    <section class="mt-5 rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div>
                <div class="text-sm font-bold text-slate-900">Bugünün programı</div>
                <div class="mt-0.5 text-xs text-slate-500">Saate göre (en fazla 24 kayıt)</div>
            </div>
            <a href="{{ route('admin.randevular.index', ['durum' => 'aktif']) }}" class="shrink-0 text-xs font-bold text-sky-700 hover:text-sky-900">Tümü →</a>
        </div>
        <div class="mt-3 space-y-2 max-h-[min(28rem,60vh)] overflow-y-auto pr-1">
            @forelse($todayTimeline as $a)
                @php
                    $durumRaw = $a->getRawOriginal('durum') ?? 'bekliyor';
                    $badge = match ($durumRaw) {
                        'bekliyor' => 'bg-amber-100 text-amber-900',
                        'tamamlandi' => 'bg-emerald-100 text-emerald-900',
                        'iptal' => 'bg-sky-200 text-sky-950',
                        'gelmedi' => 'bg-rose-100 text-rose-900',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    $durumLabel = match ($durumRaw) {
                        'bekliyor' => 'Bekliyor',
                        'tamamlandi' => 'Tamamlandı',
                        'iptal' => 'İptal',
                        'gelmedi' => 'Gelmedi',
                        default => $durumRaw,
                    };
                @endphp
                <div class="flex gap-3 rounded-2xl border border-sky-100/80 bg-white/60 px-3 py-2.5">
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
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-slate-900">{{ $a->user?->name ?? 'Hasta' }}</div>
                        <div class="truncate text-[11px] text-slate-500">{{ $a->doctor?->user?->name ?? $a->doctor?->title ?? 'Doktor' }} @if($a->doctor?->department) · {{ $a->doctor->department->name }} @endif</div>
                    </div>
                    <span class="shrink-0 self-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $badge }}">{{ $durumLabel }}</span>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-sky-200 bg-white/50 py-8 text-center text-sm text-slate-500">
                    Bugün için slotu olan randevu yok.
                </div>
            @endforelse
        </div>
    </section>
@endsection

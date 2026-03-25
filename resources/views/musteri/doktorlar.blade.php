@extends('layouts.musteri')

@section('title', 'Doktorlar')

@section('content')
    <div class="mb-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-gradient-to-br from-white/95 via-sky-50/45 to-emerald-50/35 p-5 shadow-md surface-elevated sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/95 shadow-sm sm:flex" aria-hidden="true">
                    <svg class="h-8 w-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Uzman kadro</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-sky-950">Doktorlar</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">Birime göre hekimleri inceleyin; tek tıkla randevu akışına geçin.</p>
                </div>
            </div>
            <a href="{{ route('musteri.randevu.al') }}"
               class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/25">
                Randevu al
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($doctors as $doc)
            @php
                $doctorName = $doc->user?->name ?? trim(($doc->title ?: 'Doktor').' — '.$doc->department?->name);
            @endphp
            <div class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm surface-elevated ui-soft-rise">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-900 truncate">{{ $doctorName }}</div>
                        <div class="mt-1 text-xs text-slate-600">
                            {{ $doc->department?->name ?? 'Birim' }} · {{ $doc->title ?? '' }}
                        </div>
                        @if (!empty($doc->bio))
                            <div class="mt-3 text-xs text-slate-600 leading-relaxed line-clamp-3">
                                {{ $doc->bio }}
                            </div>
                        @endif
                    </div>
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-3xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M8 2.5H12C12.8 2.5 13.5 3.2 13.5 4V6.2C13.5 6.7 13.7 7.1 14 7.4L15.2 8.6C15.6 9 15.8 9.5 15.8 10V15.2C15.8 16.1 15.1 16.8 14.2 16.8H5.8C4.9 16.8 4.2 16.1 4.2 15.2V10C4.2 9.5 4.4 9 4.8 8.6L6 7.4C6.3 7.1 6.5 6.7 6.5 6.2V4C6.5 3.2 7.2 2.5 8 2.5Z" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3">
                    <div class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                        Randevu açık
                    </div>
                    <a href="{{ route('musteri.randevu.al', ['department_id' => $doc->department_id, 'doctor_id' => $doc->id]) }}"
                       class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        Randevu
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-sky-200 bg-white/70 hospital-glass p-10 text-center sm:col-span-2 lg:col-span-3">
                <div class="text-sm font-semibold text-slate-700">Doktor bulunamadı.</div>
                <div class="mt-2 text-sm text-slate-600">Bölüm veya randevu durumlarını kontrol edin.</div>
            </div>
        @endforelse
    </div>
@endsection


@extends('layouts.musteri')

@section('title', 'Favorilerim')

@section('content')
    <div class="mb-6 rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-sky-950">Favorilerim</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-600">
            Kaydettiğiniz hastane ve polikliniklere buradan tek tıkla randevu akışına gidebilirsiniz. Randevu alırken favorileri <strong class="font-semibold text-slate-800">tarih ve saat adımının sonunda</strong> güncelleyebilirsiniz.
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-sky-700">Favori hastaneler</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($favoriteHospitals as $h)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-sky-100 bg-white/60 px-4 py-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900">{{ $h->name }}</div>
                            @if ($h->city)
                                <div class="mt-0.5 text-xs text-slate-500">{{ $h->city }}</div>
                            @endif
                            @if (! $h->is_active)
                                <div class="mt-1 text-xs font-medium text-amber-800">Bu kayıt pasif; yine de listede duruyor.</div>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($h->is_active)
                                <a href="{{ route('musteri.randevu.al', ['hospital_id' => $h->id]) }}"
                                   class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                                    Randevu al
                                </a>
                            @endif
                            <form method="post" action="{{ route('musteri.favori.hastane.toggle') }}" class="inline">
                                @csrf
                                <input type="hidden" name="hospital_id" value="{{ $h->id }}">
                                <button type="submit" class="rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-sky-50 transition">
                                    Favoriden çıkar
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-sky-200 bg-white/50 px-4 py-6 text-sm text-slate-600">
                        Henüz favori hastane yok. Randevu alırken doktor ve tarih seçtikten sonra, son adımda favorilere ekleyebilirsiniz.
                    </li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-3xl border border-emerald-100/80 bg-white/75 hospital-glass p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Favori poliklinikler</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($favoriteClinics as $row)
                    @php
                        $h = $row['hospital'];
                        $d = $row['department'];
                    @endphp
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-100/90 bg-emerald-50/20 px-4 py-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900">{{ $d->name }}</div>
                            <div class="mt-0.5 text-xs text-slate-600">{{ $h->name }}@if ($h->city) · {{ $h->city }} @endif</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($h->is_active && $d->is_active)
                                <a href="{{ route('musteri.randevu.al', ['hospital_id' => $h->id, 'department_id' => $d->id]) }}"
                                   class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                                    Randevu al
                                </a>
                            @endif
                            <form method="post" action="{{ route('musteri.favori.poliklinik.toggle') }}" class="inline">
                                @csrf
                                <input type="hidden" name="hospital_id" value="{{ $h->id }}">
                                <input type="hidden" name="department_id" value="{{ $d->id }}">
                                <button type="submit" class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-emerald-50/80 transition">
                                    Favoriden çıkar
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-emerald-200 bg-white/50 px-4 py-6 text-sm text-slate-600">
                        Henüz favori poliklinik yok. Randevu alırken son adımda (tarih ve saat bölümünde) polikliniği favorilere ekleyebilirsiniz.
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('musteri.randevu.al') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Randevu al →</a>
    </div>
@endsection

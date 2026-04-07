@extends('layouts.musteri')

@section('title', 'Yetkili olduklarım')

@section('content')
    <div class="mb-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-gradient-to-br from-white/95 via-indigo-50/35 to-emerald-50/30 p-5 shadow-md surface-elevated sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Vekil randevu</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-sky-950">Yetkili olduklarım</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Bu sayfada yetkili kişiler sadece <strong class="font-semibold text-slate-800">manuel</strong> eklenir.
                    Hastanın <strong class="font-semibold text-slate-800">ad, soyad, T.C. kimlik no, seri no ve cinsiyet</strong> bilgilerini girerek kaydedin.
                    Randevu alırken listeden kişinin üstüne tıklayıp onun adına randevu oluşturabilirsiniz.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                    Randevu al
                </a>
                <a href="{{ route('musteri.panel') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200/80 bg-white/85 px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60">
                    Panele dön
                </a>
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[minmax(0,1fr),minmax(320px,360px)] lg:items-start">
        <div class="space-y-6">
            <section class="rounded-3xl border border-sky-100/80 bg-white/75 hospital-glass p-5 shadow-sm sm:p-6">
                <h2 class="text-sm font-bold uppercase tracking-wide text-sky-800">Manuel eklenenler (son eklenen en üstte)</h2>
                <p class="mt-1 text-xs text-slate-500">Sağdaki formdan ad, soyad, T.C., seri no ve cinsiyet ile ekleyin; randevu alırken isimlerine tıklayarak seçebilirsiniz.</p>
                @if ($manuelProxyHastalar->isEmpty())
                    <div class="mt-4 rounded-2xl border border-dashed border-sky-200 bg-sky-50/40 p-5 text-sm text-slate-600">
                        Henüz manuel kayıt yok.
                    </div>
                @else
                    <ul class="mt-4 space-y-3" role="list">
                        @foreach ($manuelProxyHastalar as $h)
                            @php
                                $tc = (string) ($h->tc_kimlik_no ?? '');
                                $tcGoster = strlen($tc) >= 4 ? '***'.substr($tc, -4) : '—';
                            @endphp
                            <li class="rounded-2xl border border-sky-200/80 bg-white/80 p-4 shadow-sm">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-slate-900">{{ $h->name }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">
                                            T.C. {{ $tcGoster }}
                                            @if($h->pivot?->kimlik_seri_no) · Seri: {{ $h->pivot->kimlik_seri_no }} @endif
                                            @if($h->pivot?->kimlik_dogum_tarihi) · Doğum: {{ \Carbon\Carbon::parse($h->pivot->kimlik_dogum_tarihi)->format('d.m.Y') }} @endif
                                            @if($h->pivot?->kimlik_cinsiyet) · Cinsiyet: {{ $h->pivot->kimlik_cinsiyet }} @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('musteri.randevu.al', ['hasta_id' => $h->id]) }}"
                                           class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                                            Randevu al
                                        </a>
                                        <a href="{{ route('musteri.randevu.gecmis', ['hasta_id' => $h->id]) }}"
                                           class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-3 py-2 text-xs font-bold text-slate-800 hover:bg-sky-50 transition">
                                            Geçmiş
                                        </a>
                                        <form method="post" action="{{ route('musteri.yetkili.hasta.kaldir') }}" class="inline" onsubmit="return confirm('Listeden kaldırmak istiyor musunuz?');">
                                            @csrf
                                            <input type="hidden" name="patient_user_id" value="{{ $h->id }}">
                                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-900 hover:bg-rose-100 transition">
                                                Kaldır
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <section class="rounded-3xl border border-indigo-100/90 bg-gradient-to-br from-indigo-50/50 to-white/90 hospital-glass p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
            <h2 class="text-sm font-bold uppercase tracking-wide text-indigo-900">Kimlik bilgileri ile hasta ekle</h2>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                Hastanın <strong class="font-semibold text-slate-800">ad, soyad, T.C. kimlik no, seri no</strong> ve <strong class="font-semibold text-slate-800">cinsiyet</strong> bilgilerini girin. T.C. + ad/soyad + cinsiyet, sistem kaydıyla eşleşmelidir.
            </p>
            <form method="post" action="{{ route('musteri.yetkili.hasta.ekle') }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="kimlik_ad" class="mb-1 block text-sm font-medium text-slate-700">Ad</label>
                        <input type="text" name="kimlik_ad" id="kimlik_ad" value="{{ old('kimlik_ad') }}" required autocomplete="off"
                               class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    </div>
                    <div>
                        <label for="kimlik_soyad" class="mb-1 block text-sm font-medium text-slate-700">Soyad</label>
                        <input type="text" name="kimlik_soyad" id="kimlik_soyad" value="{{ old('kimlik_soyad') }}" required autocomplete="off"
                               class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    </div>
                    <div>
                        <label for="tc_kimlik_no" class="mb-1 block text-sm font-medium text-slate-700">T.C. kimlik numarası</label>
                        <input type="text" name="tc_kimlik_no" id="tc_kimlik_no" inputmode="numeric" autocomplete="off" maxlength="11" pattern="[0-9]{11}" required
                               value="{{ old('tc_kimlik_no') }}"
                               placeholder="11 hane"
                               class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    </div>
                    <div>
                        <label for="seri_no" class="mb-1 block text-sm font-medium text-slate-700">Seri no</label>
                        <input type="text" name="seri_no" id="seri_no" value="{{ old('seri_no') }}" required autocomplete="off" maxlength="32"
                               placeholder="Örn: A12B3456"
                               class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    </div>
                    <div>
                        <label for="cinsiyet" class="mb-1 block text-sm font-medium text-slate-700">Cinsiyet</label>
                        <select name="cinsiyet" id="cinsiyet" required class="ui-focus-ring w-full rounded-2xl border border-sky-200 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                            <option value="">Seçin</option>
                            <option value="E" @selected(old('cinsiyet') === 'E')>Erkek (E)</option>
                            <option value="K" @selected(old('cinsiyet') === 'K')>Kadın (K)</option>
                            <option value="D" @selected(old('cinsiyet') === 'D')>Diğer (D)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="ui-focus-ring w-full rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 transition">
                    Kaydet ve listeye ekle
                </button>
            </form>
        </section>
    </div>
@endsection

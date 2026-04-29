@extends('layouts.hospital')

@section('title', 'Bölüm başkanlığı paneli')
@section('subtitle', ($doctors->first()?->department?->name ?? 'Bölüm').' · '.$headUser->managedHospital?->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900">Bölüm ayarı</h2>
            <p class="mt-1 text-xs text-slate-600">65 yaş üstü hasta kuralı ve mesai sonuna taşıma ayarı.</p>
            <form method="post" action="{{ route('bolum-baskanligi.ayarlar.guncelle') }}" class="mt-4 grid gap-3 md:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-[11px] font-semibold text-slate-700" for="senior_age_threshold">65+ yaş eşiği</label>
                    <input type="number" name="senior_age_threshold" id="senior_age_threshold" min="50" max="120"
                           value="{{ old('senior_age_threshold', $settings->senior_age_threshold ?? 65) }}"
                           class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs text-slate-900">
                </div>
                <div class="grid content-center gap-2">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="auto_transfer_senior" value="0">
                        <input type="checkbox" name="auto_transfer_senior" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('auto_transfer_senior', $settings->auto_transfer_senior ?? false))>
                        65+ hastayı uygun başka doktora aktar
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="mesai_tasima_aktif" value="0">
                        <input type="checkbox" name="mesai_tasima_aktif" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('mesai_tasima_aktif', $settings->mesai_tasima_aktif ?? true))>
                        Mesai sonuna yaklaşan randevuyu en yakın tarihe taşı
                    </label>
                </div>
                <button type="submit" class="md:col-span-2 rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
                    Kaydet
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900">Doktor seçerek işlem yap</h2>
            <p class="mt-1 text-xs text-slate-600">Aşağıdan doktor seçip randevu gününü neden yazarak erteleyebilir veya izin işlemini uygulayabilirsin.</p>
            <div class="mt-3 grid items-start gap-3 md:grid-cols-2">
                <form method="post" action="{{ route('bolum-baskanligi.doktor.secim.randevu-erteleme') }}" class="rounded-xl border border-indigo-200/70 bg-white p-3 space-y-2">
                    @csrf
                    @method('PUT')
                    <label class="text-[11px] font-semibold text-slate-700">Doktor seç</label>
                    <select name="doctor_id" required class="w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs text-slate-900">
                        <option value="">Seçin</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->user?->name ?? 'Doktor #'.$doctor->id }}</option>
                        @endforeach
                    </select>
                    <div>
                        <label class="text-[11px] font-semibold text-slate-700" for="erteleme_tarihi">Ertelenecek gün</label>
                        <input type="date" name="erteleme_tarihi" id="erteleme_tarihi" required class="mt-1 w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs text-slate-900" />
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-slate-700" for="erteleme_nedeni">Neden (zorunlu, en fazla 512 karakter)</label>
                        <textarea name="erteleme_nedeni" id="erteleme_nedeni" required rows="3" maxlength="512" placeholder="Örn: toplantı, eğitim, acil görev vb."
                                  class="mt-1 w-full rounded-xl border border-indigo-200 bg-white px-3 py-2 text-xs text-slate-900">{{ old('erteleme_nedeni') }}</textarea>
                    </div>
                    <button type="submit" class="rounded-xl border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-900">Randevu gününü ertele</button>
                </form>

                <form method="post" action="{{ route('bolum-baskanligi.doktor.secim.izin') }}" class="rounded-xl border border-emerald-200/70 bg-white p-3 space-y-2">
                    @csrf
                    @method('PUT')
                    <label class="text-[11px] font-semibold text-slate-700">Doktor seç</label>
                    <select name="doctor_id" required class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs text-slate-900">
                        <option value="">Seçin</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->user?->name ?? 'Doktor #'.$doctor->id }}</option>
                        @endforeach
                    </select>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <input type="date" name="leave_date" required class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs text-slate-900" />
                        <input type="text" name="leave_note" placeholder="Not" class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs text-slate-900" />
                    </div>
                    <button type="submit" class="rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-900">İzin kaydet</button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <p class="text-xs text-slate-600">Ana sayfada doktor kartları kaldırıldı. Doktor detayları için doktor listesi sayfasını kullanın.</p>
            <a href="{{ route('bolum-baskanligi.doktorlar.index') }}" class="mt-3 inline-flex rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 hover:bg-sky-100 transition">
                Doktor listesine git
            </a>
        </div>
    </div>
@endsection

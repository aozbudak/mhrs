@extends('layouts.musteri')

@section('title', 'Profil')

@section('content')
    <div class="mb-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-gradient-to-br from-white/95 via-sky-50/40 to-emerald-50/30 p-5 shadow-md surface-elevated sm:p-6">
        <div class="flex min-w-0 items-start gap-4">
            <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/95 shadow-sm sm:flex" aria-hidden="true">
                <svg class="h-8 w-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Hesabınız</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-sky-950">Profil</h1>
                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">İletişim ve kimlik bilgilerinizi güncel tutun; şifrenizi isteğe bağlı değiştirin.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <section class="rounded-3xl border border-sky-200/50 bg-white/75 hospital-glass p-5 shadow-lg shadow-sky-900/[0.04] lg:col-span-2">
            <form method="post" action="{{ route('musteri.profil.guncelle') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <div class="text-sm font-semibold text-slate-900">Profili düzenle</div>
                    <p class="mt-0.5 text-xs text-slate-600">T.C. kimlik numaranız güvenlik nedeniyle salt okunur.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold text-sky-700">Ad Soyad</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                               class="mt-1.5 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2"
                               autocomplete="name">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-sky-700">E-posta</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                               class="mt-1.5 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2"
                               autocomplete="email">
                    </div>
                    <div>
                        <label for="tc_kimlik_no" class="block text-xs font-bold text-sky-700">T.C. Kimlik No</label>
                        <input id="tc_kimlik_no" type="text" readonly value="{{ $user->tc_kimlik_no }}"
                               class="mt-1.5 w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600"
                               aria-readonly="true">
                    </div>
                <div>
                    <label for="phone" class="block text-xs font-bold text-sky-700">Telefon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                           class="mt-1.5 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2"
                           autocomplete="tel">
                </div>
                    <div>
                        <label for="birth_date_display" class="block text-xs font-bold text-sky-700">Doğum tarihi</label>
                        <input id="birth_date_display" type="text" readonly value="{{ $user->birth_date?->format('d.m.Y') ?? '—' }}"
                               class="mt-1.5 w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600"
                               aria-readonly="true">
                        <p class="mt-1 text-[11px] text-slate-500">Kayıt sırasında belirlenir; değiştirilemez.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-sky-700">Öncelikli hasta bilgisi</label>
                        <div class="mt-1.5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                            @if($user->engelli)
                                <span class="font-semibold text-emerald-800">Kayıtta engelli olarak bildirildi.</span>
                            @else
                                <span>Kayıtta engelli olarak bildirilmedi.</span>
                            @endif
                            @if($user->birth_date && $user->birth_date->diffInYears(now()) >= \App\Models\User::ONCELIKLI_YAS_ESIGI)
                                <span class="mt-1 block text-xs text-slate-600">{{ \App\Models\User::ONCELIKLI_YAS_ESIGI }} yaş ve üzeri (doğum tarihine göre) öncelik kapsamındasınız.</span>
                            @endif
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Bu alanlar güvenlik ve tutarlılık için yalnızca kayıt ekranından belirlenir.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="gender" class="block text-xs font-bold text-sky-700">Cinsiyet</label>
                        <select id="gender" name="gender"
                                class="mt-1.5 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2">
                            <option value="" @selected((string) old('gender', $user->gender ?? '') === '')>Seçiniz</option>
                            <option value="E" @selected(old('gender', $user->gender) === 'E')>Erkek</option>
                            <option value="K" @selected(old('gender', $user->gender) === 'K')>Kadın</option>
                            <option value="D" @selected(old('gender', $user->gender) === 'D')>Diğer</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-sky-200 bg-white/50 p-4">
                    <div class="text-xs font-bold text-sky-700">Şifre değiştir (isteğe bağlı)</div>
                    <p class="mt-1 text-[11px] text-slate-600">Yeni şifre yazarsanız mevcut şifrenizi de girmeniz gerekir.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="current_password" class="block text-xs font-semibold text-slate-700">Mevcut şifre</label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                   class="mt-1 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2">
                        </div>
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700">Yeni şifre</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                   class="mt-1 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Yeni şifre (tekrar)</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                   class="mt-1 w-full rounded-2xl border border-sky-100 bg-white/80 px-4 py-2.5 text-sm text-slate-900 shadow-sm outline-none ring-sky-200 focus:ring-2">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                        Kaydet
                    </button>
                    <a href="{{ route('musteri.panel') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Vazgeç</a>
                </div>
            </form>
        </section>

        <aside class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
            <div class="text-sm font-semibold text-slate-900">Hızlı işlemler</div>
            <div class="mt-3 space-y-3">
                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    Randevu al
                </a>
                <a href="{{ route('musteri.randevu.gecmis') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-white/70 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    Geçmiş randevular
                </a>
                <a href="{{ route('musteri.panel') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-white/70 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    Randevularım
                </a>
            </div>
        </aside>
    </div>
@endsection

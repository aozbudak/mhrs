@extends('layouts.hospital')

@section('title', 'Profil')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm sm:p-6">
            <div class="flex min-w-0 items-start gap-4">
                <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/95 shadow-sm sm:flex" aria-hidden="true">
                    <svg class="h-7 w-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Hesabınız</p>
                    <h2 class="mt-1 text-lg font-extrabold text-slate-900">Profil</h2>
                    <p class="mt-1 text-sm text-slate-600">Ad, e-posta ve telefon bilgilerinizi güncelleyin; şifre değişikliği isteğe bağlıdır.</p>
                </div>
            </div>
        </div>

        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm sm:p-6">
            <form method="post" action="{{ route($kr.'.profil.guncelle') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="text-xs font-bold text-slate-700">Ad soyad</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                               autocomplete="name"
                               class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                    </div>
                    <div>
                        <label for="email" class="text-xs font-bold text-slate-700">E-posta</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                               autocomplete="email"
                               class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                    </div>
                    <div>
                        <label for="phone" class="text-xs font-bold text-slate-700">Telefon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                               autocomplete="tel"
                               class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                    </div>
                    <div class="sm:col-span-2 rounded-2xl border border-slate-200/80 bg-slate-50/60 px-4 py-3 text-xs text-slate-600">
                        <span class="font-semibold text-slate-800">T.C. kimlik no</span>
                        <span class="mt-1 block font-mono text-sm text-slate-900">{{ $user->tc_kimlik_no }}</span>
                        <p class="mt-1 text-[11px] text-slate-500">Sistem tarafından atanır; değiştirilemez.</p>
                    </div>
                </div>

                <div class="border-t border-sky-100/80 pt-5 space-y-4">
                    <p class="text-xs font-bold text-slate-800">Şifre değiştir</p>
                    <p class="text-xs text-slate-600">Yeni şifre belirlemek için önce mevcut şifrenizi girin.</p>
                    <div>
                        <label for="current_password" class="text-xs font-bold text-slate-700">Mevcut şifre</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                               class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="text-xs font-bold text-slate-700">Yeni şifre</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                   class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-xs font-bold text-slate-700">Yeni şifre (tekrar)</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                   class="mt-1.5 w-full rounded-2xl border border-sky-200/90 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition">
                        Kaydet
                    </button>
                    <a href="{{ route($kr.'.panel') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-white/80 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/70 transition">
                        Panele dön
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection

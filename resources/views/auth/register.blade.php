@extends('layouts.auth')

@section('title', 'Kayıt')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 md:items-stretch">
        <!-- Left medical illustration -->
        <aside class="hidden md:block">
            <div class="h-full rounded-3xl hospital-glass border border-sky-100/80 bg-white/60 p-8 shadow-sm">
                <div class="flex items-center gap-2 rounded-full border border-sky-200/70 bg-sky-50/60 px-4 py-2 text-sm font-semibold text-sky-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-sky-200 bg-white/60">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10 2.2C6.7 2.2 4 4.8 4 8C4 12.4 10 17.8 10 17.8C10 17.8 16 12.4 16 8C16 4.8 13.3 2.2 10 2.2Z" stroke="#0ea5e9" stroke-width="2" />
                            <path d="M7.6 8.1L9.2 9.7L12.4 6.5" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    Güvenli Üyelik
                </div>

                <h1 class="mt-6 text-3xl font-extrabold leading-tight text-sky-950">Hasta kaydı</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    Adım adım; güvenli ve sakin bir kayıt deneyimi.
                </p>

                <div class="mt-8 grid gap-4">
                    <div class="rounded-2xl border border-sky-100 bg-white/60 p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-sky-50/70">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Temiz form</div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    Sadece gerekli alanlar. İkonlarla destekli.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50/70 to-emerald-50/60 p-4">
                        <div class="text-sm font-semibold text-sky-950">Gizlilik odaklı</div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            Şifreler güvenli şekilde saklanır (backend mevcut güvenli kurallarıyla).
                        </div>
                    </div>
                </div>

                <div class="mt-7 rounded-2xl border border-sky-100 bg-white/60 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M20 26C20 18 26 12 34 12C42 12 48 18 48 26C48 38 34 52 34 52C34 52 20 38 20 26Z" stroke="#0ea5e9" stroke-width="3"/>
                                <path d="M28 26H40" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                                <path d="M34 20V32" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Sade & profesyonel</div>
                            <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                Hastane arayüzü hissi; sakin renk paleti ve yuvarlatılmış elemanlar.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right register form -->
        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-7 shadow-sm md:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-sky-950">Kayıt</h1>
                    <p class="mt-1 text-sm text-slate-600">Hasta hesabınızı oluşturun.</p>
                </div>
                <div class="hidden sm:block rounded-2xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-800">
                    Sağlık kalitesi
                </div>
            </div>

            <form method="post" action="{{ route('register') }}" class="mt-6 space-y-4" aria-label="Kayıt formu">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Ad</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                autocomplete="given-name"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="surname" class="mb-1 block text-sm font-medium text-slate-700">Soyad</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="surname"
                                id="surname"
                                value="{{ old('surname') }}"
                                required
                                autocomplete="family-name"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tc_kimlik_no" class="mb-1 block text-sm font-medium text-slate-700">T.C. kimlik no</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="4" y="3" width="12" height="14" rx="2" stroke="#0ea5e9" stroke-width="1.8"/>
                                <path d="M7 8H13M7 11H13M7 14H10" stroke="#10b981" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="tc_kimlik_no"
                            id="tc_kimlik_no"
                            value="{{ old('tc_kimlik_no') }}"
                            required
                            inputmode="numeric"
                            maxlength="11"
                            autocomplete="off"
                            class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            placeholder="11 haneli T.C. kimlik numaranız"
                        >
                    </div>
                    @error('tc_kimlik_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">E-posta</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M3.5 5.5H16.5V14.5H3.5V5.5Z" stroke="#0ea5e9" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M3.8 6.1L10 10.6L16.2 6.1" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                        >
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Şifre</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.2 9.1V6.7C6.2 4.9 7.7 3.4 9.5 3.4C11.3 3.4 12.8 4.9 12.8 6.7V9.1" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M5.4 9.1H13.6C14.3 9.1 14.9 9.7 14.9 10.4V15.1C14.9 15.8 14.3 16.4 13.6 16.4H5.4C4.7 16.4 4.1 15.8 4.1 15.1V10.4C4.1 9.7 4.7 9.1 5.4 9.1Z" stroke="#10b981" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Şifre tekrar</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.2 9.1V6.7C6.2 4.9 7.7 3.4 9.5 3.4C11.3 3.4 12.8 4.9 12.8 6.7V9.1" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M5.4 9.1H13.6C14.3 9.1 14.9 9.7 14.9 10.4V15.1C14.9 15.8 14.3 16.4 13.6 16.4H5.4C4.7 16.4 4.1 15.8 4.1 15.1V10.4C4.1 9.7 4.7 9.1 5.4 9.1Z" stroke="#10b981" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition"
                >
                    Kayıt ol
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Zaten hesabınız var mı?
                <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-900">Giriş yapın</a>
            </p>
        </section>
    </div>
@endsection

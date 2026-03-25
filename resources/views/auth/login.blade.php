@extends('layouts.auth')

@section('title', 'Giriş')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 md:items-stretch">
        <!-- Left medical illustration -->
        <aside class="hidden md:block">
            <div class="h-full rounded-3xl hospital-glass border border-sky-100/80 bg-white/60 p-8 shadow-sm">
                <div class="flex items-center gap-2 rounded-full border border-sky-200/70 bg-sky-50/60 px-4 py-2 text-sm font-semibold text-sky-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200/70">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10 2L15 5V10C15 14 12 17 10 18C8 17 5 14 5 10V5L10 2Z" stroke="#0ea5e9" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M7.6 10L9.1 11.6L12.6 8.1" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    Ortak giriş — hasta ve yönetim
                </div>

                <h1 class="mt-6 text-3xl font-extrabold leading-tight text-sky-950">
                    Tek ekrandan güvenli giriş
                </h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    Hasta: T.C. kimlik no ve şifre; yönetici: e-posta ve şifre ile doğrulanır. Ardından rolünüze uygun panele yönlendirilirsiniz.
                </p>

                <div class="mt-8 grid gap-4">
                    <div class="rounded-2xl border border-sky-100 bg-white/60 p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-sky-50/70">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M8 2.5H12C12.8 2.5 13.5 3.2 13.5 4V6.2C13.5 6.7 13.7 7.1 14 7.4L15.2 8.6C15.6 9 15.8 9.5 15.8 10V15.2C15.8 16.1 15.1 16.8 14.2 16.8H5.8C4.9 16.8 4.2 16.1 4.2 15.2V10C4.2 9.5 4.4 9 4.8 8.6L6 7.4C6.3 7.1 6.5 6.7 6.5 6.2V4C6.5 3.2 7.2 2.5 8 2.5Z" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M10 6.2V9.6" stroke="#10b981" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M8.2 8H11.8" stroke="#10b981" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Rol bazlı yönlendirme</div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    Hasta hesabı randevu paneline, yönetici hesabı yönetim özetine gider.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-100 bg-white/60 p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50/70">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 1.8C6.8 1.8 4.2 4.4 4.2 7.6C4.2 12 10 18.2 10 18.2C10 18.2 15.8 12 15.8 7.6C15.8 4.4 13.2 1.8 10 1.8Z" stroke="#10b981" stroke-width="1.8"/>
                                    <path d="M7.8 7.7L9.2 9.1L12.2 6.1" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Akıcı adım akışı</div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    Poliklinik -> doktor -> saat seçiminde net yönlendirme.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50/70 to-emerald-50/60 p-4">
                        <div class="text-sm font-semibold text-sky-950">Hızlı ipucu</div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            “Beni hatırla” seçeneği sonraki girişlerinizi kısaltır.
                        </div>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="mt-7 rounded-2xl border border-sky-100 bg-white/60 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="20" y="12" width="24" height="40" rx="12" stroke="#0ea5e9" stroke-width="3"/>
                                <path d="M32 12V52" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                                <path d="M16 28C26 22 38 22 48 28" stroke="#0ea5e9" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="24" cy="36" r="4" fill="rgba(14,165,233,0.18)" stroke="#0ea5e9" stroke-width="3"/>
                                <circle cx="40" cy="36" r="4" fill="rgba(16,185,129,0.18)" stroke="#10b981" stroke-width="3"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Tıbbi bilgi, sade arayüz</div>
                            <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                Sessiz renkler, yumuşak gölgeler ve erişilebilir kontroller.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right login form -->
        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-7 shadow-sm md:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-sky-950">Giriş</h1>
                    <p class="mt-1 text-sm text-slate-600">Hasta veya yönetici sekmesini seçin; ardından kimlik alanları ve şifrenizi girin.</p>
                </div>
                <div class="hidden sm:block rounded-2xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-800">
                    Güvenli oturum
                </div>
            </div>

            <p class="mt-4 rounded-2xl border border-sky-100 bg-white/60 px-4 py-3 text-xs leading-relaxed text-slate-600">
                <span class="font-semibold text-slate-800">Hasta:</span> T.C. kimlik no + şifre.
                <span class="font-semibold text-slate-800">Yönetici:</span> e-posta + şifre.
            </p>

            <form method="post" action="{{ route('login') }}" class="mt-6 space-y-4" aria-label="Giriş formu" id="loginForm">
                @csrf

                <div class="flex rounded-2xl border border-sky-200 bg-sky-50/50 p-1" role="tablist" aria-label="Giriş türü">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="login_as" value="patient" class="peer sr-only" @checked(old('login_as', 'patient') !== 'admin')>
                        <span class="block rounded-xl px-3 py-2 text-center text-sm font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-sky-950 peer-checked:shadow-sm">Hasta</span>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="login_as" value="admin" class="peer sr-only" @checked(old('login_as') === 'admin')>
                        <span class="block rounded-xl px-3 py-2 text-center text-sm font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-sky-950 peer-checked:shadow-sm">Yönetici</span>
                    </label>
                </div>

                <div class="rounded-2xl border border-sky-200/90 bg-white/70 p-4 shadow-sm ring-1 ring-sky-100/80">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Hesap bilgileri</p>

                    <div class="space-y-3">
                        <div id="patientLoginFields" class="@if(old('login_as') === 'admin') hidden @endif">
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
                                    inputmode="numeric"
                                    maxlength="11"
                                    autocomplete="off"
                                    @if(old('login_as', 'patient') !== 'admin') autofocus @endif
                                    class="w-full rounded-xl border border-sky-200/90 bg-white pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                                    placeholder="11 hane"
                                >
                            </div>
                            @error('tc_kimlik_no')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="adminLoginFields" class="@if(old('login_as') !== 'admin') hidden @endif">
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
                                    autocomplete="email"
                                    @if(old('login_as') === 'admin') autofocus @endif
                                    class="w-full rounded-xl border border-sky-200/90 bg-white pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

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
                            autocomplete="current-password"
                            class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                        >
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-sky-300 text-emerald-600 focus:ring-emerald-500/20"
                        >
                        Beni hatırla
                    </label>

                    <a href="#" data-forgot-password class="text-sm font-medium text-emerald-700 hover:text-emerald-900">
                        Şifremi unuttum
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition"
                >
                    Giriş yap
                </button>
            </form>

            <div class="mt-6 flex items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    Hasta hesabınız yok mu?
                    <a href="{{ route('register') }}" class="ml-1 font-medium text-emerald-700 hover:text-emerald-900">Kayıt olun</a>
                    <span class="mt-1 block text-xs text-slate-500">Yönetici erişimi kayıt ile verilmez.</span>
                </div>
                <div class="hidden sm:block rounded-2xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-800">
                    Yumuşak arayüz
                </div>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var patientFields = document.getElementById('patientLoginFields');
            var adminFields = document.getElementById('adminLoginFields');
            var radios = document.querySelectorAll('input[name="login_as"]');
            var tcInput = document.getElementById('tc_kimlik_no');
            var emailInput = document.getElementById('email');

            function sync() {
                var isAdmin = document.querySelector('input[name="login_as"]:checked')?.value === 'admin';
                if (!patientFields || !adminFields) return;
                patientFields.classList.toggle('hidden', isAdmin);
                adminFields.classList.toggle('hidden', !isAdmin);
                if (tcInput) {
                    tcInput.required = !isAdmin;
                    tcInput.toggleAttribute('disabled', isAdmin);
                }
                if (emailInput) {
                    emailInput.required = isAdmin;
                    emailInput.toggleAttribute('disabled', !isAdmin);
                }
            }

            radios.forEach(function (r) { r.addEventListener('change', sync); });
            sync();
        })();
    </script>
@endsection

@extends('layouts.auth')

@section('title', 'Giriş')

@section('content')
    <div class="mx-auto w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-sky-200/80 bg-white/70 px-4 py-2 text-sm font-bold text-sky-950 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/40">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 2L15 5V10C15 14 12 17 10 18C8 17 5 14 5 10V5L10 2Z" stroke="#0ea5e9" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M7.6 10L9.1 11.6L12.6 8.1" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ config('app.name', 'MHRS sistemi') }}
            </a>
            <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-sky-950">Giriş</h1>
            <p class="mt-2 text-sm text-slate-600">Hasta T.C. kimlik no; yönetici ve kurum (hastane / sağlık merkezi) hesapları e-posta ile giriş yapar. Kurum girişinde yönlendirme, atanmış kurum türünüze göre otomatik yapılır.</p>
        </div>

        <div class="rounded-3xl border border-sky-100/80 bg-white/80 hospital-glass p-6 shadow-lg shadow-sky-900/[0.06] sm:p-8">
            <form method="post" action="{{ route('login') }}" class="space-y-5" aria-label="Giriş formu" id="loginForm">
                @csrf

                @php
                    $loginAsQuery = request()->query('login_as');
                    $loginAsDefault = in_array($loginAsQuery, ['patient', 'admin', 'hospital_admin'], true) ? $loginAsQuery : 'patient';
                    $loginAsOld = old('login_as', $loginAsDefault);
                    $emailLoginActive = in_array($loginAsOld, ['admin', 'hospital_admin'], true);
                @endphp

                <div class="flex rounded-2xl border border-sky-200 bg-sky-50/50 p-1" role="tablist" aria-label="Giriş türü">
                    <label class="min-w-0 flex-1 cursor-pointer">
                        <input type="radio" name="login_as" value="patient" class="peer sr-only" @checked($loginAsOld === 'patient')>
                        <span class="block rounded-xl px-1.5 py-2.5 text-center text-[11px] font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-sky-950 peer-checked:shadow-sm sm:px-2 sm:text-sm">Hasta</span>
                    </label>
                    <label class="min-w-0 flex-1 cursor-pointer">
                        <input type="radio" name="login_as" value="admin" class="peer sr-only" @checked($loginAsOld === 'admin')>
                        <span class="block rounded-xl px-1.5 py-2.5 text-center text-[11px] font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-sky-950 peer-checked:shadow-sm sm:px-2 sm:text-sm">Yönetici</span>
                    </label>
                    <label class="min-w-0 flex-1 cursor-pointer">
                        <input type="radio" name="login_as" value="hospital_admin" class="peer sr-only" @checked($loginAsOld === 'hospital_admin')>
                        <span class="block rounded-xl px-1.5 py-2.5 text-center text-[11px] font-semibold text-slate-600 transition peer-checked:bg-white peer-checked:text-sky-950 peer-checked:shadow-sm sm:px-2 sm:text-sm">Kurum</span>
                    </label>
                </div>

                <div class="space-y-4">
                    <div id="patientLoginFields" class="@if($emailLoginActive) hidden @endif">
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
                                @if(! $emailLoginActive) name="tc_kimlik_no" @endif
                                id="tc_kimlik_no"
                                value="{{ old('tc_kimlik_no') }}"
                                inputmode="numeric"
                                maxlength="11"
                                autocomplete="off"
                                @if($loginAsOld === 'patient') autofocus @endif
                                class="w-full rounded-xl border border-sky-200/90 bg-white pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                                placeholder="11 hane"
                            >
                        </div>
                        @error('tc_kimlik_no')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="emailLoginFields" class="@if(! $emailLoginActive) hidden @endif">
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
                                @if($emailLoginActive) name="email" @endif
                                id="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                @if($emailLoginActive) autofocus @endif
                                class="w-full rounded-xl border border-sky-200/90 bg-white pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                class="w-full rounded-xl border border-sky-200 bg-white/90 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
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
                    <a href="#" data-forgot-password class="text-sm font-medium text-emerald-700 hover:text-emerald-900 sm:text-right">
                        Şifremi unuttum
                    </a>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition"
                >
                    Giriş yap
                </button>
            </form>

            <p class="mt-6 border-t border-sky-100 pt-6 text-center text-sm text-slate-600">
                Hasta hesabınız yok mu?
                <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">Kayıt olun</a>
            </p>
            <p class="mt-2 text-center text-xs text-slate-500">Yönetici ve kurum erişimi kayıt ile verilmez.</p>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            <a href="{{ url('/') }}" class="font-medium text-sky-700 hover:text-sky-900">Ana sayfaya dön</a>
        </p>
    </div>

    <script>
        (function () {
            var patientFields = document.getElementById('patientLoginFields');
            var emailFields = document.getElementById('emailLoginFields');
            var radios = document.querySelectorAll('input[name="login_as"]');
            var tcInput = document.getElementById('tc_kimlik_no');
            var emailInput = document.getElementById('email');

            function sync() {
                var v = document.querySelector('input[name="login_as"]:checked')?.value || 'patient';
                var useEmail = (v === 'admin' || v === 'hospital_admin');
                if (!patientFields || !emailFields) return;
                patientFields.classList.toggle('hidden', useEmail);
                emailFields.classList.toggle('hidden', !useEmail);
                if (tcInput) {
                    tcInput.required = !useEmail;
                    if (useEmail) {
                        tcInput.removeAttribute('name');
                    } else {
                        tcInput.setAttribute('name', 'tc_kimlik_no');
                    }
                }
                if (emailInput) {
                    emailInput.required = useEmail;
                    if (useEmail) {
                        emailInput.setAttribute('name', 'email');
                    } else {
                        emailInput.removeAttribute('name');
                    }
                }
            }

            radios.forEach(function (r) { r.addEventListener('change', sync); });
            sync();
        })();
    </script>
@endsection

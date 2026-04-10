<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kurum paneli') — {{ config('app.name', 'MHRS sistemi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @include('partials.vite-head')
    <style>
        .admin-soft-shadow {
            box-shadow: 0 18px 45px -28px rgba(14, 165, 233, 0.42);
        }
        .admin-lift {
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }
        .admin-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px -22px rgba(14, 165, 233, 0.55);
        }
    </style>
</head>
@php
    $__hUser = auth('hospital')->user()?->fresh();
    $__hName = $__hUser?->managedHospital?->name;
    $__kr = request()->routeIs('saglik-merkezi.*') ? 'saglik-merkezi' : 'hastane';
    $__kurumEtiket = request()->routeIs('saglik-merkezi.*') ? 'Sağlık merkezi' : 'Hastane';
    $isKurumOzet = request()->routeIs('hastane.panel') || request()->routeIs('saglik-merkezi.panel');
    $isKurumDoktorlar = request()->routeIs('hastane.doktorlar') || request()->routeIs('saglik-merkezi.doktorlar');
    $isKurumAyarlar = request()->routeIs('hastane.ayarlar*') || request()->routeIs('saglik-merkezi.ayarlar*');
    $isKurumRandevular = request()->routeIs('hastane.randevular.*') || request()->routeIs('saglik-merkezi.randevular.*');
    $isKurumProfil = request()->routeIs('hastane.profil*') || request()->routeIs('saglik-merkezi.profil*');
@endphp
<body class="min-h-screen hospital-bg antialiased text-slate-900"
      style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="hospital-ambient" aria-hidden="true"></div>
    <div class="hospital-grain" aria-hidden="true"></div>
    <div class="relative z-10">
        <div id="hospital-sidebar-backdrop"
             class="fixed inset-0 z-30 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
             aria-hidden="true"></div>

        <div class="flex min-h-screen">
            <aside id="hospital-sidebar"
                   class="fixed inset-y-0 left-0 z-40 flex h-screen min-h-0 w-64 max-w-[min(100vw-3rem,16rem)] flex-col overflow-hidden border-r bg-white/90 hospital-glass shadow-xl transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0 lg:max-w-none admin-soft-shadow {{ request()->routeIs('saglik-merkezi.*') ? 'border-teal-200/70 shell-saglik-merkezi-accent' : 'border-sky-200/60' }}">
                <div class="flex h-16 shrink-0 items-center gap-3 border-b border-sky-100/80 px-4">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border {{ request()->routeIs('saglik-merkezi.*') ? 'border-teal-200 bg-gradient-to-br from-teal-50 to-sky-50' : 'border-sky-200 bg-gradient-to-br from-sky-50 to-emerald-50' }}">
                        @if(request()->routeIs('saglik-merkezi.*'))
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-8h6v8" stroke="#0d9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="#0ea5e9" stroke-width="2"/>
                                <path d="M8 14h6M11 11v3" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wide {{ request()->routeIs('saglik-merkezi.*') ? 'text-teal-700' : 'text-sky-600' }}">{{ $__kurumEtiket }}</div>
                        <div class="truncate text-sm font-extrabold text-slate-950">{{ config('app.name', 'MHRS sistemi') }}</div>
                    </div>
                    <button type="button" id="hospital-sidebar-close" class="ml-auto rounded-xl p-2 text-slate-500 hover:bg-sky-50 lg:hidden" aria-label="Menüyü kapat">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-3 py-4" aria-label="Kurum menü">
                    <a href="{{ route($__kr.'.panel') }}"
                       class="hospital-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isKurumOzet ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isKurumOzet ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                        </span>
                        Özet
                    </a>
                    <a href="{{ route($__kr.'.doktorlar') }}"
                       class="hospital-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isKurumDoktorlar ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isKurumDoktorlar ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        Doktorlar
                    </a>
                    <a href="{{ route($__kr.'.ayarlar') }}"
                       class="hospital-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isKurumAyarlar ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isKurumAyarlar ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        Çalışma saatleri
                    </a>
                    <a href="{{ route($__kr.'.randevular.index') }}"
                       class="hospital-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isKurumRandevular ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isKurumRandevular ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        Randevular
                    </a>
                    <a href="{{ route($__kr.'.profil') }}"
                       class="hospital-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isKurumProfil ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isKurumProfil ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        Profil
                    </a>
                </nav>

                <div class="shrink-0 border-t border-sky-100/80 px-3 py-3">
                    <a href="{{ url('/') }}" class="admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-slate-500 hover:bg-white/70 hover:text-slate-800 transition">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100/80 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </span>
                        Siteye dön
                    </a>
                </div>

                <div class="shrink-0 border-t border-sky-100/80 p-3">
                    <div class="rounded-2xl border border-sky-100 bg-white/60 px-3 py-2.5">
                        @if ($__hName)
                            <div class="truncate text-[11px] font-bold uppercase tracking-wide text-sky-600">Kurum adı</div>
                            <div class="truncate text-xs font-bold text-slate-800">{{ $__hName }}</div>
                        @endif
                        @if ($__hUser)
                            <div class="mt-2 truncate text-[11px] text-slate-500">{{ $__hUser->email }}</div>
                        @endif
                        <form method="post" action="{{ route($__kr.'.logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-sky-200 bg-white/80 py-2 text-xs font-semibold text-slate-700 hover:bg-sky-50 transition">
                                Çıkış
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="hidden w-64 shrink-0 lg:block" aria-hidden="true"></div>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b bg-white/80 hospital-glass px-4 shadow-sm backdrop-blur-sm lg:px-6 admin-soft-shadow {{ request()->routeIs('saglik-merkezi.*') ? 'border-teal-100/90' : 'border-sky-100/80' }}">
                    <button type="button" id="hospital-sidebar-open" class="rounded-2xl border border-sky-200 bg-white/80 p-2.5 text-slate-700 shadow-sm hover:bg-sky-50 lg:hidden" aria-label="Menüyü aç">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-base font-extrabold text-slate-950 lg:text-lg">@yield('title', 'Özet')</h1>
                        @hasSection('subtitle')
                            <p class="truncate text-xs text-slate-500">@yield('subtitle')</p>
                        @elseif ($__hName)
                            <p class="truncate text-xs text-slate-500">{{ $__hName }}</p>
                        @endif
                    </div>
                    <a href="{{ url('/') }}" class="hidden rounded-2xl border border-sky-100 bg-white/60 px-3 py-2 text-xs font-semibold text-sky-800 hover:bg-sky-50/80 sm:inline-flex">
                        Ana sayfa
                    </a>
                </header>

                <main class="flex-1 px-4 py-5 lg:px-6 lg:py-6">
                    @if (session('success'))
                        <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 hospital-glass">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-3xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 rounded-3xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

    <script>
        (function () {
            var sidebar = document.getElementById('hospital-sidebar');
            var backdrop = document.getElementById('hospital-sidebar-backdrop');
            var openBtn = document.getElementById('hospital-sidebar-open');
            var closeBtn = document.getElementById('hospital-sidebar-close');
            if (!sidebar || !backdrop) return;

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                document.body.classList.add('overflow-hidden');
            }
            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden');
            }

            openBtn && openBtn.addEventListener('click', openMenu);
            closeBtn && closeBtn.addEventListener('click', closeMenu);
            backdrop.addEventListener('click', closeMenu);

            document.querySelectorAll('.hospital-panel-nav-item').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (window.matchMedia('(max-width: 1023px)').matches) closeMenu();
                });
            });
        })();
    </script>
    @include('partials.vite-cdn-body')
    @stack('scripts')
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Paneli') — {{ config('app.name', 'MHRS sistemi') }}</title>
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
<body class="min-h-screen hospital-bg antialiased text-slate-900"
      style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="hospital-ambient" aria-hidden="true"></div>
    <div class="hospital-grain" aria-hidden="true"></div>
    <div class="relative z-10">
    <div id="admin-sidebar-backdrop"
         class="fixed inset-0 z-30 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
         aria-hidden="true"></div>

    <div class="flex min-h-screen">
        <aside id="admin-sidebar"
               class="fixed inset-y-0 left-0 z-40 flex w-64 max-w-[min(100vw-3rem,16rem)] flex-col border-r border-sky-200/60 bg-white/90 hospital-glass shadow-xl transition-transform duration-200 ease-out -translate-x-full lg:static lg:translate-x-0 lg:max-w-none lg:shadow-none admin-soft-shadow">
            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-sky-100/80 px-4">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                    <svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="#0ea5e9" stroke-width="2"/>
                        <path d="M11 6.6V15.4M6.6 11H15.4" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-sky-600">Yönetim</div>
                    <div class="truncate text-sm font-extrabold text-slate-950">{{ config('app.name', 'MHRS sistemi') }}</div>
                </div>
                <button type="button" id="admin-sidebar-close" class="ml-auto rounded-xl p-2 text-slate-500 hover:bg-sky-50 lg:hidden" aria-label="Menüyü kapat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Admin menü">
                <a href="{{ route('admin.panel') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.panel') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.panel') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    Yönetim
                </a>
                <a href="{{ route('admin.randevular.index') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.randevular.*') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.randevular.*') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    Randevular
                </a>
                <a href="{{ route('admin.bildirimler.index') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.bildirimler.*') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.bildirimler.*') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    Bildirim Yönetimi
                </a>
                <a href="{{ route('admin.doktorlar.index') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.doktorlar.*') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.doktorlar.*') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    Doktorlar
                </a>
                <a href="{{ route('admin.hastaneler.index') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.hastaneler.*') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.hastaneler.*') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8M12 17v4M4 21h16M5 21V9a2 2 0 012-2h10a2 2 0 012 2v12M9 9V7a3 3 0 016 0v2"/></svg>
                    </span>
                    Hastaneler
                </a>
                <a href="{{ route('admin.saglik-merkezleri.index') }}"
                   class="admin-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.saglik-merkezleri.*') ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('admin.saglik-merkezleri.*') ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 6h10M7 14h10M10 18h4"/></svg>
                    </span>
                    Sağlık Merkezleri
                </a>

                <div class="my-4 border-t border-sky-100/80"></div>
                <a href="{{ url('/') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-slate-500 hover:bg-white/70 hover:text-slate-800 transition">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100/80 text-slate-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </span>
                    Siteye dön
                </a>
            </nav>

            <div class="shrink-0 border-t border-sky-100/80 p-3">
                <div class="rounded-2xl border border-sky-100 bg-white/60 px-3 py-2.5">
                    @php
                        $__adminUser = auth('admin')->user()?->fresh();
                    @endphp
                    <div class="truncate text-xs font-bold text-slate-800">{{ $__adminUser?->name }}</div>
                    <div class="truncate text-[11px] text-slate-500">{{ $__adminUser?->email }}</div>
                    @if ($__adminUser)
                        <dl class="mt-2 space-y-1 border-t border-sky-100/80 pt-2 text-[10px] leading-snug text-slate-600">
                            <div class="flex gap-1">
                                <dt class="shrink-0 font-semibold text-slate-700">Son giriş</dt>
                                <dd class="min-w-0 truncate">{{ $__adminUser->last_login_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                            <div class="flex gap-1">
                                <dt class="shrink-0 font-semibold text-slate-700">Son çıkış</dt>
                                <dd class="min-w-0 truncate">{{ $__adminUser->last_logout_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                        </dl>
                    @endif
                    <form method="post" action="{{ route('admin.logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-sky-200 bg-white/80 py-2 text-xs font-semibold text-slate-700 hover:bg-sky-50 transition">
                            Çıkış
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b border-sky-100/80 bg-white/80 hospital-glass px-4 shadow-sm backdrop-blur-sm lg:px-6 admin-soft-shadow">
                <button type="button" id="admin-sidebar-open" class="rounded-2xl border border-sky-200 bg-white/80 p-2.5 text-slate-700 shadow-sm hover:bg-sky-50 lg:hidden" aria-label="Menüyü aç">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-base font-extrabold text-slate-950 lg:text-lg">@yield('title', 'Admin')</h1>
                    @hasSection('subtitle')
                        <p class="truncate text-xs text-slate-500">@yield('subtitle')</p>
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
            var sidebar = document.getElementById('admin-sidebar');
            var backdrop = document.getElementById('admin-sidebar-backdrop');
            var openBtn = document.getElementById('admin-sidebar-open');
            var closeBtn = document.getElementById('admin-sidebar-close');
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

            document.querySelectorAll('.admin-nav-item').forEach(function (a) {
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

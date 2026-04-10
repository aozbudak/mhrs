<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hasta paneli') — {{ config('app.name', 'MHRS sistemi') }}</title>
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

@php
    use App\Support\MusteriAccess;
    $musteriUser = MusteriAccess::user();
    $unreadCount = $musteriUser?->unreadNotifications()->count() ?? 0;

    $isPanel = request()->routeIs('musteri.panel');
    $isAileHekimi = request()->routeIs('musteri.aile-hekimi');
    $isProfile = request()->routeIs('musteri.profil');
    $isGecmisRandevular = request()->routeIs('musteri.randevu.gecmis');
    $isBildirimler = request()->routeIs('musteri.bildirimler.*');
    $isFavoriler = request()->routeIs('musteri.favoriler');
    $isYetkiliOlduklarim = request()->routeIs('musteri.yetkili-olduklarim');
    $isSaglikMerkeziRandevu = request()->routeIs('musteri.randevu.al') && request()->query('kurum_tipi') === 'saglik_merkezi';
    $isRandevuAl = request()->routeIs('musteri.randevu.al');

    $musteriInitials = '?';
    if ($musteriUser && trim((string) $musteriUser->name) !== '') {
        $parts = preg_split('/\s+/u', trim((string) $musteriUser->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $musteriInitials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $musteriInitials .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
        }
    }

    $successFlash = session('success');
    $successText = $successFlash !== null ? (string) $successFlash : '';
    $successIsRandevu = $successText !== ''
        && str_contains(mb_strtolower($successText, 'UTF-8'), 'randevu');
@endphp

<div class="hospital-ambient" aria-hidden="true"></div>
<div class="hospital-grain" aria-hidden="true"></div>

<div class="relative z-10">
    <div id="musteri-sidebar-backdrop"
         class="fixed inset-0 z-30 bg-slate-900/40 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
         aria-hidden="true"></div>

    <div class="flex min-h-screen">
        <aside id="musteri-sidebar"
               class="fixed inset-y-0 left-0 z-40 flex h-screen min-h-0 w-64 max-w-[min(100vw-3rem,16rem)] flex-col overflow-hidden border-r border-sky-200/60 bg-white/90 hospital-glass shadow-xl transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0 lg:max-w-none admin-soft-shadow">
            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-sky-100/80 px-4">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                    <svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="#0ea5e9" stroke-width="2"/>
                        <path d="M11 6.6V15.4M6.6 11H15.4" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-wide text-sky-600">Hasta</div>
                    <div class="truncate text-sm font-extrabold text-slate-950">{{ config('app.name', 'MHRS sistemi') }}</div>
                </div>
                <button type="button" id="musteri-sidebar-close" class="ml-auto rounded-xl p-2 text-slate-500 hover:bg-sky-50 lg:hidden" aria-label="Menüyü kapat">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-3 py-4" aria-label="Hasta menü">
                <a href="{{ route('musteri.panel') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isPanel ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isPanel ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    Randevularım
                </a>
                <a href="{{ route('musteri.randevu.gecmis') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isGecmisRandevular ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isGecmisRandevular ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    Geçmiş randevular
                </a>
                <a href="{{ route('musteri.bildirimler.index') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isBildirimler ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isBildirimler ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </span>
                    Bildirimler
                </a>
                <a href="{{ route('musteri.favoriler') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isFavoriler ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isFavoriler ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </span>
                    Favoriler
                </a>
                <a href="{{ route('musteri.randevu.al', ['kurum_tipi' => 'saglik_merkezi']) }}"
                   class="musteri-panel-nav-item admin-lift flex items-start gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isSaglikMerkeziRandevu ? 'bg-gradient-to-r from-teal-50 via-sky-50 to-emerald-50/90 text-teal-950 shadow-sm ring-1 ring-teal-200/60' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isSaglikMerkeziRandevu ? 'bg-white text-teal-700 shadow-sm' : 'bg-teal-50/80 text-teal-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block leading-snug">Sağlık merkezi randevusu</span>
                        <span class="mt-0.5 block text-[11px] font-medium leading-snug text-slate-500 {{ $isSaglikMerkeziRandevu ? 'text-teal-800/80' : '' }}">Poliklinik ve bakım birimlerinden seçim</span>
                    </span>
                </a>
                <a href="{{ route('musteri.yetkili-olduklarim') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isYetkiliOlduklarim ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isYetkiliOlduklarim ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    Yetkili olduklarım
                </a>
                <a href="{{ route('musteri.aile-hekimi') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isAileHekimi ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isAileHekimi ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </span>
                    Aile hekimi
                </a>
                <a href="{{ route('musteri.profil') }}"
                   class="musteri-panel-nav-item admin-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isProfile ? 'bg-gradient-to-r from-sky-100 to-emerald-50/80 text-sky-950 shadow-sm' : 'text-slate-600 hover:bg-white/70' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $isProfile ? 'bg-white text-sky-700' : 'bg-sky-50/80 text-sky-600' }}">
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
                    @if ($musteriUser)
                        @php($__hastaOturum = $musteriUser->fresh())
                        <div class="truncate text-xs font-bold text-slate-800">{{ $__hastaOturum->name }}</div>
                        <div class="truncate text-[11px] text-slate-500">{{ $__hastaOturum->email }}</div>
                        <dl class="mt-2 space-y-1 border-t border-sky-100/80 pt-2 text-[10px] leading-snug text-slate-600">
                            <div class="flex gap-1">
                                <dt class="shrink-0 font-semibold text-slate-700">Son giriş</dt>
                                <dd class="min-w-0 truncate">{{ $__hastaOturum->last_login_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                            <div class="flex gap-1">
                                <dt class="shrink-0 font-semibold text-slate-700">Son çıkış</dt>
                                <dd class="min-w-0 truncate">{{ $__hastaOturum->last_logout_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                        </dl>
                    @endif
                    <form method="post" action="{{ route('logout') }}" class="mt-2">
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
            <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-2 border-b border-sky-100/80 bg-white/80 hospital-glass px-3 shadow-sm backdrop-blur-sm sm:gap-3 sm:px-4 lg:px-6 admin-soft-shadow">
                <button type="button" id="musteri-sidebar-open" class="shrink-0 rounded-2xl border border-sky-200 bg-white/80 p-2.5 text-slate-700 shadow-sm hover:bg-sky-50 lg:hidden" aria-label="Menüyü aç">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                @if ($musteriUser)
                    <span class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-emerald-500 text-xs font-extrabold text-white shadow-sm ring-2 ring-white/80 sm:inline-flex" aria-hidden="true">{{ $musteriInitials }}</span>
                @endif
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-base font-extrabold text-slate-950 lg:text-lg">@yield('title', 'Hasta paneli')</h1>
                    @hasSection('subtitle')
                        <p class="truncate text-xs text-slate-500">@yield('subtitle')</p>
                    @elseif ($musteriUser)
                        <p class="truncate text-xs text-slate-500">Hoş geldin, {{ $musteriUser->name }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                    <a href="{{ route('musteri.bildirimler.index') }}"
                       class="admin-lift relative inline-flex h-9 w-9 items-center justify-center rounded-xl border border-sky-200 bg-white/80 text-slate-700 shadow-sm hover:bg-sky-50 sm:h-10 sm:w-10"
                       aria-label="Bildirimlere git">
                        <svg class="h-[18px] w-[18px] sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10 18C11.1046 18 12 17.1046 12 16H8C8 17.1046 8.89543 18 10 18Z" fill="#10b981"/>
                            <path d="M16 14V11C16 7.13401 13.866 4 10 4C6.13401 4 4 7.13401 4 11V14L2.5 15.5H17.5L16 14Z" stroke="#0ea5e9" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        @if ($unreadCount > 0)
                            <span class="absolute -right-0.5 -top-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-emerald-600 px-0.5 text-[10px] font-bold text-white sm:h-5 sm:min-w-5 sm:px-1 sm:text-[11px]">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('musteri.profil') }}"
                       class="admin-lift hidden items-center gap-1.5 rounded-xl border border-sky-200 bg-white/80 px-2.5 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-sky-50 md:inline-flex">
                        <svg class="h-4 w-4 shrink-0 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil
                    </a>
                    <a href="{{ url('/') }}" class="hidden rounded-xl border border-sky-100 bg-white/60 px-2.5 py-1.5 text-xs font-semibold text-sky-800 hover:bg-sky-50/80 lg:inline-flex">
                        Ana sayfa
                    </a>
                </div>
            </header>

            <main @class([
                'flex-1 w-full min-w-0 px-4 py-5 lg:px-6 lg:py-6 hospital-content-enter',
                'max-w-5xl mx-auto' => ! $isRandevuAl,
            ])>
                @if ($successFlash)
                    <div class="mb-6 overflow-hidden rounded-3xl border border-emerald-200/90 bg-gradient-to-br from-emerald-50/95 via-white/85 to-sky-50/40 hospital-glass shadow-md shadow-emerald-600/10">
                        <div class="flex items-start gap-3 border-b border-emerald-100/70 bg-white/50 px-4 py-3.5 sm:px-5 sm:py-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-500/20" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="min-w-0 pt-0.5">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">{{ $successIsRandevu ? 'Hızlı randevu' : 'Başarılı' }}</p>
                                <h2 class="mt-0.5 text-base font-extrabold tracking-tight text-sky-950 sm:text-lg">
                                    {{ $successIsRandevu ? 'Randevunuz kaydedildi' : 'İşleminiz tamamlandı' }}
                                </h2>
                            </div>
                        </div>
                        <div class="px-4 py-3 text-sm leading-relaxed text-emerald-950 sm:px-5 sm:py-3.5">
                            {{ $successText }}
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
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
        var sidebar = document.getElementById('musteri-sidebar');
        var backdrop = document.getElementById('musteri-sidebar-backdrop');
        var openBtn = document.getElementById('musteri-sidebar-open');
        var closeBtn = document.getElementById('musteri-sidebar-close');
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

        document.querySelectorAll('.musteri-panel-nav-item').forEach(function (a) {
            a.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 1023px)').matches) closeMenu();
            });
        });
    })();
</script>
@include('partials.vite-cdn-body')
</div>
</body>
</html>

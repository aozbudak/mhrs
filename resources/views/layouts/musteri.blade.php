<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Müşteri paneli') — {{ config('app.name', 'MHRS sistemi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @include('partials.vite-head')
    <style>
        .modern-soft-shadow {
            box-shadow: 0 16px 40px -24px rgba(2, 132, 199, 0.45);
        }
        .modern-hover-lift {
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
        }
        .modern-hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px -20px rgba(14, 165, 233, 0.5);
        }
        .modern-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .modern-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #38bdf8, #10b981);
            border-radius: 999px;
        }
        .modern-scrollbar::-webkit-scrollbar-track {
            background: rgba(226, 232, 240, 0.55);
            border-radius: 999px;
        }
    </style>
</head>
<body class="min-h-screen hospital-bg text-slate-900 antialiased"
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

<div class="relative z-10 min-h-screen">
    <div class="mx-auto max-w-7xl px-2 sm:px-4">
        <div class="flex min-h-screen gap-4">
            <!-- Desktop Sidebar -->
            <aside class="relative hidden w-[272px] flex-col overflow-hidden rounded-3xl border border-sky-200/60 bg-white/65 hospital-glass p-4 shadow-lg shadow-sky-900/[0.04] modern-soft-shadow modern-scrollbar lg:flex">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-400 via-sky-500 to-emerald-500 opacity-90" aria-hidden="true"></div>
                <a href="{{ route('musteri.panel') }}" class="relative mt-1 flex items-center gap-3 rounded-2xl px-3 py-2.5 ring-1 ring-sky-100/80 bg-gradient-to-br from-white/95 to-sky-50/50 shadow-sm transition hover:shadow-md">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="url(#g2)" stroke-width="2"/>
                        <path d="M11 6.6V15.4" stroke="url(#g2)" stroke-width="2" stroke-linecap="round"/>
                        <path d="M6.6 11H15.4" stroke="url(#g2)" stroke-width="2" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="g2" x1="4" y1="4" x2="18" y2="18" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0ea5e9"/>
                                <stop offset="1" stop-color="#10b981"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="text-sm font-bold tracking-tight text-sky-950">{{ config('app.name', 'MHRS sistemi') }}</span>
                </a>

                <nav class="mt-5 flex flex-col gap-1.5">
                    <a href="{{ route('musteri.panel') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isPanel ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isPanel ? 'border-emerald-200/70 text-emerald-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        Randevularım
                    </a>
                    <a href="{{ route('musteri.randevu.gecmis') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isGecmisRandevular ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isGecmisRandevular ? 'border-emerald-200/70 text-emerald-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        Geçmiş randevular
                    </a>
                    <a href="{{ route('musteri.bildirimler.index') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isBildirimler ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isBildirimler ? 'border-emerald-200/70 text-emerald-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </span>
                        Bildirimler
                    </a>
                    <a href="{{ route('musteri.favoriler') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isFavoriler ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isFavoriler ? 'border-amber-200/70 text-amber-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </span>
                        Favoriler
                    </a>
                    <a href="{{ route('musteri.randevu.al', ['kurum_tipi' => 'saglik_merkezi']) }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isSaglikMerkeziRandevu ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isSaglikMerkeziRandevu ? 'border-emerald-200/70 text-emerald-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 6h10M7 14h10M10 18h4"/></svg>
                        </span>
                        Sağlık merkezi randevu
                    </a>
                    <a href="{{ route('musteri.yetkili-olduklarim') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isYetkiliOlduklarim ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isYetkiliOlduklarim ? 'border-indigo-200/70 text-indigo-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        Yetkili olduklarım
                    </a>
                    <a href="{{ route('musteri.aile-hekimi') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isAileHekimi ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isAileHekimi ? 'border-violet-200/70 text-violet-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </span>
                        Aile hekimi
                    </a>
                    <a href="{{ route('musteri.profil') }}"
                       class="modern-hover-lift flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition
                            {{ $isProfile ? 'bg-gradient-to-r from-sky-50 to-emerald-50/80 text-sky-950 shadow-sm ring-1 ring-sky-200/70' : 'text-slate-700 hover:bg-sky-50/70 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-sky-200/80 bg-white/80 text-sky-600 {{ $isProfile ? 'border-emerald-200/70 text-emerald-700' : '' }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        Profil
                    </a>
                </nav>

                @if ($musteriUser)
                    @php($__hastaOturum = $musteriUser->fresh())
                    <div class="mt-4 rounded-2xl border border-sky-100/90 bg-white/70 px-3 py-2.5 text-[11px] text-slate-600">
                        <div class="text-[10px] font-bold uppercase tracking-wide text-sky-700">Oturum</div>
                        <dl class="mt-2 space-y-1">
                            <div class="flex flex-wrap items-baseline gap-x-1.5 gap-y-0">
                                <dt class="font-semibold text-slate-700">Son giriş</dt>
                                <dd>{{ $__hastaOturum->last_login_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-wrap items-baseline gap-x-1.5 gap-y-0">
                                <dt class="font-semibold text-slate-700">Son çıkış</dt>
                                <dd>{{ $__hastaOturum->last_logout_at?->format('d.m.Y H:i') ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <div class="mt-auto pt-4">
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-2xl border border-sky-200 bg-white/70 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                            Çıkış
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Column -->
            <div class="flex w-full flex-col">
                <!-- Topbar -->
                <header class="relative sticky top-0 z-20 mb-4 overflow-hidden rounded-b-3xl border border-sky-200/50 bg-white/85 hospital-glass shadow-lg shadow-sky-900/[0.06] backdrop-blur-md px-3 py-3 sm:px-4 modern-soft-shadow">
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-sky-300/50 to-transparent" aria-hidden="true"></div>
                    <div class="relative flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button id="musteriMenuBtn"
                                    type="button"
                                    class="modern-hover-lift lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-white/75 hover:bg-sky-50/70 transition"
                                    aria-label="Menüyü aç"
                                    aria-controls="musteriDrawer"
                                    aria-expanded="false">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 5H17" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M3 10H17" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M3 15H17" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>

                            <div class="hidden items-center gap-3 sm:flex">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-emerald-500 text-sm font-extrabold text-white shadow-md shadow-sky-500/25 ring-2 ring-white/80" aria-hidden="true">{{ $musteriInitials }}</span>
                                <div class="min-w-0">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Hasta paneli</div>
                                    <div class="truncate text-sm font-bold text-slate-900">Hoş geldin, {{ $musteriUser->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Notifications -->
                            <div>
                                <a id="notifBtn"
                                   href="{{ route('musteri.bildirimler.index') }}"
                                   class="modern-hover-lift relative inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-white/75 hover:bg-sky-50/70 transition"
                                   aria-label="Bildirimlere git">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M10 18C11.1046 18 12 17.1046 12 16H8C8 17.1046 8.89543 18 10 18Z" fill="#10b981"/>
                                        <path d="M16 14V11C16 7.13401 13.866 4 10 4C6.13401 4 4 7.13401 4 11V14L2.5 15.5H17.5L16 14Z" stroke="#0ea5e9" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>

                                    @if ($unreadCount > 0)
                                        <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1 text-[11px] font-bold text-white">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>

                            <a href="{{ route('musteri.profil') }}"
                               class="modern-hover-lift hidden sm:flex items-center gap-2 rounded-2xl border border-sky-200 bg-white/75 px-3 py-2 hover:bg-sky-50/70 transition">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2"/>
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <span class="text-sm font-semibold text-slate-800">Profil</span>
                            </a>
                        </div>
                    </div>
                </header>

                <!-- Mobile drawer -->
                <div id="musteriDrawer" class="fixed inset-0 z-30 hidden">
                    <div class="absolute inset-0 bg-slate-900/30" data-drawer-close></div>
                    <div class="absolute left-0 top-0 h-full w-[280px] border-r border-sky-100/80 bg-white/85 hospital-glass backdrop-blur p-4 shadow-2xl shadow-sky-900/10">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold text-slate-900">Menü</div>
                            <button type="button"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-white/70 hover:bg-sky-50/60 transition"
                                    data-drawer-close
                                    aria-label="Menüyü kapat">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M5 5L15 15" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M15 5L5 15" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-4 mb-4 rounded-2xl border border-sky-100/90 bg-white/80 p-3">
                            <div class="text-[11px] font-bold uppercase tracking-wide text-sky-600">Hesap</div>
                            <div class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $musteriUser?->name }}</div>
                            <div class="truncate text-xs text-slate-500">{{ $musteriUser?->email }}</div>
                            @if ($musteriUser)
                                @php($__hastaDrawerOturum = $musteriUser->fresh())
                                <dl class="mt-3 space-y-1 border-t border-sky-100/80 pt-2 text-[11px] text-slate-600">
                                    <div><span class="font-semibold text-slate-700">Son giriş:</span> {{ $__hastaDrawerOturum->last_login_at?->format('d.m.Y H:i') ?? '—' }}</div>
                                    <div><span class="font-semibold text-slate-700">Son çıkış:</span> {{ $__hastaDrawerOturum->last_logout_at?->format('d.m.Y H:i') ?? '—' }}</div>
                                </dl>
                            @endif
                        </div>

                        <nav class="mt-2 flex flex-col gap-2">
                            <a href="{{ route('musteri.panel') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isPanel ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Randevularım</a>
                            <a href="{{ route('musteri.randevu.gecmis') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isGecmisRandevular ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Geçmiş randevular</a>
                            <a href="{{ route('musteri.bildirimler.index') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isBildirimler ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Bildirimler</a>
                            <a href="{{ route('musteri.favoriler') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isFavoriler ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Favoriler</a>
                            <a href="{{ route('musteri.randevu.al', ['kurum_tipi' => 'saglik_merkezi']) }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isSaglikMerkeziRandevu ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Sağlık merkezi randevu</a>
                            <a href="{{ route('musteri.yetkili-olduklarim') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isYetkiliOlduklarim ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Yetkili olduklarım</a>
                            <a href="{{ route('musteri.aile-hekimi') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isAileHekimi ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Aile hekimi</a>
                            <a href="{{ route('musteri.profil') }}" class="rounded-2xl px-3 py-2 text-sm font-semibold transition {{ $isProfile ? 'bg-sky-100 text-sky-900' : 'text-slate-800 hover:bg-sky-50/60' }}" data-drawer-close>Profil</a>
                        </nav>

                        <div class="mt-auto pt-4">
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-2xl border border-sky-200 bg-white/70 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                                    Çıkış
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <main class="hospital-content-enter w-full max-w-5xl mx-auto px-3 py-8 sm:px-6 sm:py-10">
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
    </div>
</div>

<script>
    (function () {
        // Mobile drawer
        var btn = document.getElementById('musteriMenuBtn');
        var drawer = document.getElementById('musteriDrawer');
        if (btn && drawer) {
            var closeEls = drawer.querySelectorAll('[data-drawer-close]');

            btn.addEventListener('click', function () {
                drawer.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            });

            closeEls.forEach(function (el) {
                el.addEventListener('click', function () {
                    drawer.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                });
            });
        }

    })();
</script>
    @include('partials.vite-cdn-body')
</body>
</html>


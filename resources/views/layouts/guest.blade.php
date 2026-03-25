<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'MHRS sistemi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @include('partials.vite-head')
</head>
<body class="min-h-screen hospital-bg text-slate-900 antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="hospital-ambient" aria-hidden="true"></div>
    <div class="hospital-grain" aria-hidden="true"></div>

    <div class="relative z-10">
    @hasSection('custom_guest_header')
        @yield('custom_guest_header')
    @else
    <header class="relative border-b border-sky-200/50 bg-white/75 hospital-glass shadow-md shadow-sky-900/[0.04] backdrop-blur-md">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-sky-400 via-sky-500 to-emerald-500 opacity-90" aria-hidden="true"></div>
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="/" class="group flex items-center gap-3 rounded-2xl py-1 pr-2 transition hover:bg-sky-50/60">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-emerald-500 shadow-md shadow-sky-500/20 ring-2 ring-white/70">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="rgba(255,255,255,0.95)" stroke-width="2"/>
                    <path d="M11 6.6V15.4" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linecap="round"/>
                    <path d="M6.6 11H15.4" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linecap="round"/>
                </svg>
                </span>
                <span class="text-lg font-extrabold tracking-tight text-sky-950">{{ config('app.name', 'MHRS sistemi') }}</span>
            </a>
            <a href="/" class="inline-flex items-center rounded-2xl border border-sky-200/80 bg-white/70 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/50 hover:text-emerald-900">Ana sayfa</a>
        </div>
    </header>
    @endif

    <main class="hospital-content-enter mx-auto max-w-6xl px-4 py-10 sm:px-6">
        @if (session('error'))
            <div class="mx-auto mb-6 max-w-md rounded-lg border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mx-auto mb-6 max-w-md rounded-lg border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    @include('partials.vite-cdn-body')
    </div>
</body>
</html>

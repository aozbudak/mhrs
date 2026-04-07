<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MHRS sistemi') — {{ config('app.name', 'MHRS sistemi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @include('partials.vite-head')
</head>
<body class="min-h-screen hospital-bg antialiased text-slate-900"
      style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="hospital-ambient" aria-hidden="true"></div>
    <div class="hospital-grain" aria-hidden="true"></div>
    <div class="relative z-10 mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12 hospital-content-enter">
        @if (session('error'))
            <div class="mx-auto mb-6 max-w-md rounded-2xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto mb-6 max-w-md rounded-2xl border border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-900 hospital-glass">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        (function () {
            var els = document.querySelectorAll('[data-forgot-password]');
            els.forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    alert('Şifre sıfırlama henüz aktif değil.');
                });
            });
        })();
    </script>
    @include('partials.vite-cdn-body')
</body>
</html>


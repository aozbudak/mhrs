@php
    $viteReady = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
@endphp
@if ($viteReady)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif

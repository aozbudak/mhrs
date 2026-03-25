@php
    $viteReady = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
@endphp
@if (! $viteReady)
    {{-- DOM hazır olduktan sonra yükle: head içindeki CDN giriş sayfasında stilleri hiç üretmeyebilir --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        .hospital-bg {
            background:
                radial-gradient(900px circle at 10% -20%, rgba(56, 189, 248, 0.22), transparent 45%),
                radial-gradient(800px circle at 90% 10%, rgba(16, 185, 129, 0.18), transparent 50%),
                linear-gradient(180deg, #f0f9ff 0%, #f8fafc 55%, #ffffff 100%);
        }
        .hospital-glass {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .hospital-soft-border {
            border-color: rgba(186, 230, 253, 0.75);
        }
    </style>
@endif

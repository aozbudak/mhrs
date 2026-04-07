@extends('layouts.guest')

@section('title', 'Ana sayfa')

@section('custom_guest_header')
    @php
        $navPatient = auth('patient')->user();
        $navAdmin = auth('admin')->user();
        $navHasPatient = $navPatient instanceof \App\Models\User && $navPatient->isPatient();
        $navHasAdmin = $navAdmin instanceof \App\Models\User && $navAdmin->isAdmin();
    @endphp
    <header class="sticky top-0 z-50 border-b border-sky-200/60 bg-white/85 shadow-lg shadow-sky-900/[0.06] backdrop-blur-xl">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-500 via-cyan-400 to-emerald-500" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-6">
                <a href="{{ url('/') }}" class="group flex shrink-0 items-center gap-3 rounded-2xl py-1 pr-2 transition hover:bg-sky-50/80">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-emerald-500 shadow-lg shadow-sky-500/25 ring-2 ring-white/80">
                        <svg width="24" height="24" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="3.2" y="3.2" width="15.6" height="15.6" rx="4" stroke="rgba(255,255,255,0.95)" stroke-width="2"/>
                            <path d="M11 6.6V15.4" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6.6 11H15.4" stroke="rgba(255,255,255,0.95)" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-lg font-extrabold tracking-tight text-sky-950">{{ config('app.name', 'MHRS sistemi') }}</span>
                        <span class="block text-xs font-semibold text-slate-500">Çevrimiçi randevu</span>
                    </span>
                </a>

                <nav class="flex flex-wrap items-center gap-1 text-sm font-semibold" aria-label="Ana menü">
                    <a href="{{ url('/') }}" class="rounded-xl px-3 py-2 text-sky-900 transition hover:bg-sky-100/80">Ana sayfa</a>
                    <a href="#nasil" class="rounded-xl px-3 py-2 text-slate-600 transition hover:bg-sky-100/80 hover:text-sky-950">Nasıl çalışır</a>
                    <a href="#poliklinikler" class="rounded-xl px-3 py-2 text-slate-600 transition hover:bg-sky-100/80 hover:text-sky-950">Poliklinikler</a>
                </nav>

                <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                    @if (Route::has('login'))
                        @if ($navHasPatient || $navHasAdmin)
                            @if ($navHasPatient)
                                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700">Randevu al</a>
                            @endif
                            @if ($navHasAdmin)
                                <a href="{{ route('admin.panel') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50/60">Yönetim</a>
                            @endif
                            @if ($navHasPatient)
                                <form method="post" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Çıkış</button>
                                </form>
                            @endif
                            @if ($navHasAdmin)
                                <form method="post" action="{{ route('admin.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Çıkış</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50/60">Giriş yap</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-sky-600 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-sky-500/20 transition hover:from-sky-700 hover:to-emerald-700">Kayıt ol</a>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    @php
        $landingPatient = auth('patient')->user();
        $landingAdmin = auth('admin')->user();
        $landingHasPatient = $landingPatient instanceof \App\Models\User && $landingPatient->isPatient();
        $landingHasAdmin = $landingAdmin instanceof \App\Models\User && $landingAdmin->isAdmin();
        $deptCardPhotos = [
            'https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=640&h=280&fit=crop&q=80',
            'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=640&h=280&fit=crop&q=80',
            'https://images.unsplash.com/photo-1516549655169-cf83a01b5860?w=640&h=280&fit=crop&q=80',
        ];
    @endphp
    <div class="mx-auto max-w-6xl space-y-8">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl border border-white/20 bg-gradient-to-br from-sky-600 via-sky-500 to-emerald-600 p-6 shadow-xl shadow-sky-600/25 ring-1 ring-white/20 sm:p-8 md:p-10">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-emerald-400/30 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 opacity-[0.12]" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.55) 1px, transparent 0); background-size: 28px 28px;" aria-hidden="true"></div>

            <div class="relative grid gap-8 md:grid-cols-2 md:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">
                        <span>{{ config('app.name', 'MHRS sistemi') }}</span>
                        <span class="opacity-90">Randevu Sistemi</span>
                    </div>

                    <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">
                        Hızlı ve güvenli randevu
                    </h1>
                    <p class="mt-3 max-w-lg text-sm leading-relaxed text-white/95 sm:text-base">
                        Birim seçin, doktorun müsait saatlerini görüntüleyin ve randevunuzu oluşturun.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        @if (Route::has('login'))
                            @if ($landingHasPatient || $landingHasAdmin)
                                @if ($landingHasPatient)
                                    <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-sky-700 shadow-md transition hover:bg-sky-50">
                                        Randevu al
                                    </a>
                                @endif
                                @if ($landingHasAdmin)
                                    <a href="{{ route('admin.panel') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-sky-700 shadow-md transition hover:bg-sky-50">
                                        Yönetim paneli
                                    </a>
                                @endif

                                @if ($landingHasPatient)
                                    <form method="post" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-white/35 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                                            Çıkış
                                        </button>
                                    </form>
                                @endif
                                @if ($landingHasAdmin)
                                    <form method="post" action="{{ route('admin.logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-white/35 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                                            Çıkış
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/35 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                                    Giriş yap
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 shadow-md transition hover:bg-emerald-50">
                                        Kayıt ol
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3" aria-label="Randevu adımları">
                        @foreach ([['1', 'Birim', 'Poliklinik seç'], ['2', 'Doktor', 'Uygun hekimi seç'], ['3', 'Saat', 'Müsait zamanı onayla']] as $step)
                            <div class="flex min-w-[140px] flex-1 items-center gap-3 rounded-2xl border border-white/25 bg-white/10 px-3 py-2.5 backdrop-blur-sm sm:flex-none">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/20 text-sm font-extrabold text-white">{{ $step[0] }}</span>
                                <div>
                                    <div class="text-sm font-bold text-white">{{ $step[1] }}</div>
                                    <div class="text-xs text-white/85">{{ $step[2] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center md:justify-end">
                    <div class="relative w-full max-w-md">
                        <div class="absolute -right-2 -top-2 z-10 rounded-2xl border border-white/40 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-sm sm:-right-4 sm:-top-4">
                            <div class="text-xs font-bold uppercase tracking-wide text-emerald-700">7/24 erişim</div>
                            <div class="text-sm font-extrabold text-sky-950">Randevu planla</div>
                        </div>
                        <div class="absolute -bottom-2 -left-2 z-10 max-w-[11rem] rounded-2xl border border-white/35 bg-white/90 px-3 py-2.5 shadow-lg backdrop-blur-sm sm:-bottom-3 sm:-left-3">
                            <div class="flex items-center gap-2">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700" aria-hidden="true">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <div class="text-[11px] font-semibold text-slate-500">Güvenli kayıt</div>
                                    <div class="text-xs font-bold text-slate-800">Doğrulanmış hesap</div>
                                </div>
                            </div>
                        </div>
                        <figure class="overflow-hidden rounded-3xl border border-white/30 shadow-2xl shadow-sky-900/20 ring-2 ring-white/20">
                            <img
                                src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&h=900&fit=crop&q=85"
                                alt="Modern hastane ortamında sağlık hizmeti"
                                class="aspect-[4/5] w-full object-cover sm:aspect-[5/6]"
                                width="800"
                                height="960"
                                fetchpriority="high"
                            >
                        </figure>
                    </div>
                </div>
            </div>

            <div class="relative mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Güven unsurları">
                @foreach ([
                    ['Güvenli Akış', 'Kimlik doğrulama ile randevu'],
                    ['Kolay Kullanım', 'Adım adım randevu oluşturma'],
                    ['Şeffaf Bilgi', 'Aktif birimleri anında gör'],
                    ['Hasta Dostu', 'Müsait saatlere hızlı erişim'],
                ] as [$title, $desc])
                    <div class="rounded-2xl border border-white/20 bg-white/90 p-4 shadow-sm">
                        <div class="text-sm font-semibold text-sky-950">{{ $title }}</div>
                        <div class="mt-1 text-xs leading-relaxed text-slate-600">{{ $desc }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Görsel yardım / nasıl çalışır --}}
        <section id="nasil" class="scroll-mt-28 overflow-hidden rounded-3xl border border-sky-200/70 bg-gradient-to-br from-white/95 via-sky-50/40 to-emerald-50/35 hospital-glass shadow-md shadow-sky-200/20">
            <div class="grid gap-6 p-6 lg:grid-cols-[1fr,minmax(0,280px)] lg:items-stretch sm:p-7">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-sky-200/80 bg-white/70 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wide text-sky-800">
                        Görsel yardım
                    </div>
                    <h2 class="mt-3 text-xl font-extrabold tracking-tight text-sky-950">Randevu akışı kısaca</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                        Önce hastane ve biriminizi seçin, ardından doktor ile müsait gün / saati işaretleyin. Onay sonrası randevunuz panelinizde listelenir.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if ($landingHasPatient)
                                <a href="{{ route('musteri.randevu.al') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-700">Adımlara git</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-700">Kayıt ol</a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-sky-50">Giriş yap</a>
                        @endif
                    </div>
                </div>
                <div class="relative flex min-h-[200px] justify-center lg:justify-end">
                    <div class="relative w-full max-w-[280px] overflow-hidden rounded-3xl border border-sky-100/90 shadow-lg shadow-sky-200/30 ring-1 ring-white/80">
                        <img
                            src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=560&h=700&fit=crop&q=80"
                            alt="Randevu ve koordinasyon sürecini temsil eden sağlık ortamı"
                            class="h-full min-h-[200px] w-full object-cover"
                            width="560"
                            height="700"
                            loading="lazy"
                            decoding="async"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-sky-950/50 via-transparent to-sky-900/10"></div>
                        <div class="absolute bottom-4 left-4 right-4 rounded-2xl border border-white/40 bg-white/90 p-3 shadow-md backdrop-blur-sm">
                            <div class="flex items-center gap-2 text-sm font-bold text-sky-950">
                                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                3 adımda randevu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Poliklinikler --}}
        <section id="poliklinikler" class="scroll-mt-28 overflow-hidden rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass shadow-sm">
            <div class="flex flex-col gap-4 border-b border-sky-100/70 bg-gradient-to-b from-sky-50/80 to-white/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-sky-950">Aktif poliklinikler</h2>
                    <p class="mt-0.5 text-sm text-slate-600">Birimleri arayın ve seçin</p>
                </div>
                <div class="w-full sm:max-w-xs">
                    <label for="deptSearch" class="sr-only">Birim ara</label>
                    <input
                        type="text"
                        id="deptSearch"
                        placeholder="Birim ara (örn: Kardiyoloji)…"
                        autocomplete="off"
                        class="w-full rounded-2xl border border-sky-200 bg-white/90 px-4 py-2.5 text-sm font-medium text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"
                    >
                </div>
            </div>

            @if (!empty($departments) && $departments->count() > 0)
                <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6" id="deptGrid">
                    @foreach ($departments as $department)
                        <article
                            class="group flex flex-col overflow-hidden rounded-2xl border border-sky-100/90 bg-white/80 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200/80 hover:shadow-md"
                            data-dept-card
                            data-dept-name="{{ $department->name }}"
                        >
                            <div class="relative h-36 overflow-hidden">
                                <img
                                    src="{{ $deptCardPhotos[$loop->index % count($deptCardPhotos)] }}"
                                    alt=""
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    width="640"
                                    height="280"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/85 via-slate-900/35 to-sky-900/10"></div>
                                <div class="absolute bottom-0 left-0 right-0 flex items-end justify-between gap-2 p-4">
                                    <h3 class="text-base font-bold leading-snug text-white drop-shadow-sm">{{ $department->name }}</h3>
                                    <span class="shrink-0 rounded-full border border-white/40 bg-white/95 px-2.5 py-0.5 text-xs font-bold text-emerald-800 shadow-sm">Aktif</span>
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col gap-4 px-4 py-4">
                                <p class="flex-1 text-sm leading-relaxed text-slate-600">
                                    {{ $department->description ?: 'Açıklama yok.' }}
                                </p>
                                <div class="mt-auto">
                                    @if ($landingHasPatient)
                                            <a
                                                href="{{ route('musteri.randevu.al', ['department_id' => $department->id]) }}"
                                                class="flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                                            >
                                                Seç
                                            </a>
                                    @elseif ($landingHasAdmin)
                                            <a
                                                href="{{ route('admin.panel') }}"
                                                class="flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                            >
                                                Yönetim
                                            </a>
                                    @elseif (auth('patient')->check() || auth('admin')->check())
                                            <span class="flex w-full cursor-not-allowed items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-400" aria-disabled="true">
                                                Yetkisiz
                                            </span>
                                    @else
                                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50/50">
                                            Giriş
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mx-5 mb-5 rounded-2xl border border-dashed border-emerald-300/60 bg-emerald-50/50 px-4 py-6 text-center text-sm font-medium text-emerald-900 sm:mx-6 sm:mb-6">
                    Aktif birim bulunamadı.
                    <code class="rounded-md bg-white/80 px-1.5 py-0.5 text-xs text-slate-800">php artisan db:seed</code>
                    ile örnek veriler yükleyin.
                </div>
            @endif
        </section>
    </div>

    <script>
        (function () {
            var input = document.getElementById('deptSearch');
            var grid = document.getElementById('deptGrid');
            if (!input || !grid) return;

            var cards = grid.querySelectorAll('[data-dept-card]');
            input.addEventListener('input', function () {
                var v = (input.value || '').trim().toLowerCase();
                cards.forEach(function (c) {
                    var name = (c.getAttribute('data-dept-name') || '').toLowerCase();
                    c.style.display = name.indexOf(v) !== -1 ? '' : 'none';
                });
            });
        })();
    </script>
@endsection

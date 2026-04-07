@props([
    'title' => 'Görsel yardım',
    'subtitle' => 'Adımları takip ederek birkaç dakikada randevunuzu oluşturun.',
    'sticky' => false,
])

@php
    $steps = [
        ['n' => 1, 't' => 'İl, ilçe, hastane', 'd' => 'İsterseniz “Konumuma göre” ile yakın hastaneleri sıralayın; ya da il/ilçe seçip listeden hastaneye tıklayın. Poliklinikler hastane seçilince açılır.'],
        ['n' => 2, 't' => 'Birim', 'd' => 'Poliklinik veya bölümü seçin; liste yöneticiden gelir.'],
        ['n' => 3, 't' => 'Doktor', 'd' => 'Uygun hekimi seçin; yoksa birimi değiştirin.'],
        ['n' => 4, 't' => 'Tarih & saat', 'd' => 'Müsait gün ve saatten birini işaretleyip onaylayın.'],
    ];
@endphp

<div {{ $attributes->class([
    'rounded-3xl border border-sky-200/80 bg-gradient-to-b from-white/90 via-white/75 to-sky-50/40 hospital-glass shadow-md shadow-sky-200/20 overflow-hidden',
    'lg:sticky lg:top-24' => $sticky,
]) }}>
    <div class="relative border-b border-sky-100/80 bg-gradient-to-r from-sky-500/10 via-emerald-500/8 to-transparent px-5 py-4">
        <div class="pointer-events-none absolute -right-8 -top-10 h-28 w-28 rounded-full bg-emerald-400/20 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-6 left-1/3 h-20 w-20 rounded-full bg-sky-400/15 blur-2xl"></div>
        <div class="relative flex items-start gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-sky-200/80 bg-white/90 shadow-sm" aria-hidden="true">
                <svg class="h-7 w-7" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="8" width="28" height="32" rx="6" stroke="url(#vh1)" stroke-width="2.2"/>
                    <path d="M24 16V32" stroke="url(#vh1)" stroke-width="2.2" stroke-linecap="round"/>
                    <path d="M16 24H32" stroke="url(#vh1)" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="24" cy="38" r="3" fill="#10b981" opacity="0.9"/>
                    <defs>
                        <linearGradient id="vh1" x1="10" y1="8" x2="38" y2="40" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#0ea5e9"/>
                            <stop offset="1" stop-color="#10b981"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-extrabold uppercase tracking-wide text-sky-800">{{ $title }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $subtitle }}</p>
            </div>
        </div>
    </div>

    <div class="p-5">
        <div class="mb-5 flex justify-center" role="img" aria-label="Randevu oluşturma sürecini gösteren basit çizim">
            <svg class="h-auto w-full max-w-[220px] text-sky-600" viewBox="0 0 280 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M40 120H240" stroke="currentColor" stroke-opacity="0.25" stroke-width="2" stroke-dasharray="6 6"/>
                <rect x="32" y="36" width="56" height="44" rx="10" class="fill-white" stroke="url(#vhg)" stroke-width="2"/>
                <path d="M48 54H72M48 62H68" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                <rect x="112" y="28" width="56" height="52" rx="10" class="fill-white" stroke="url(#vhg)" stroke-width="2"/>
                <path d="M128 46H152M128 54H148M128 62H146" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                <rect x="192" y="40" width="56" height="40" rx="10" class="fill-white" stroke="url(#vhg)" stroke-width="2"/>
                <circle cx="220" cy="56" r="10" stroke="#10b981" stroke-width="2"/>
                <path d="M216 56L219 59L225 53" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M60 80 L90 100 L140 88 L200 104" stroke="url(#vhg)" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                <circle cx="60" cy="80" r="5" fill="#0ea5e9"/>
                <circle cx="140" cy="88" r="5" fill="#0ea5e9"/>
                <circle cx="200" cy="104" r="5" fill="#10b981"/>
                <defs>
                    <linearGradient id="vhg" x1="32" y1="28" x2="248" y2="120" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#38bdf8"/>
                        <stop offset="1" stop-color="#34d399"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <ol class="space-y-3">
            @foreach ($steps as $step)
                <li class="flex gap-3 rounded-2xl border border-sky-100/90 bg-white/70 px-3 py-3 shadow-sm transition hover:border-emerald-200/70 hover:shadow-md">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-50 to-emerald-50 text-xs font-extrabold text-sky-900 ring-1 ring-sky-200/60">{{ $step['n'] }}</span>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-900">{{ $step['t'] }}</div>
                        <div class="mt-0.5 text-xs leading-relaxed text-slate-600">{{ $step['d'] }}</div>
                    </div>
                </li>
            @endforeach
        </ol>

        <div class="mt-4 rounded-2xl border border-emerald-200/70 bg-gradient-to-br from-emerald-50/90 to-white/80 px-4 py-3">
            <div class="flex items-start gap-2">
                <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <p class="text-xs font-medium leading-relaxed text-emerald-950">
                    Seçtiğiniz saat <strong class="font-extrabold">anında rezerve</strong> edilir; onaydan sonra işleminizi panelden görebilirsiniz.
                </p>
            </div>
        </div>

        {{ $slot }}
    </div>
</div>

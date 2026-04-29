{{--
  $r: App\Models\Randevu (doctor.user, doctor.department, doctor.hospital ilişkileri yüklü olmalı)
  $durumEtiket: [etiket metni, tailwind rozet sınıfları]
  $cardClass, $titleClass, $subClass: isteğe bağlı görünüm
  $showIptal: iptal formu göster
  $haritaModu: 'full' | 'link' — full: gömülü harita; link: yalnızca dış bağlantılar
  $gizliRozet: küçük gizli rozeti
--}}
@php
    /** @var \App\Models\Randevu $r */
    $cardClass = $cardClass ?? 'rounded-2xl border border-sky-100 bg-white/60 p-4';
    $titleClass = $titleClass ?? 'text-sm font-semibold text-slate-900';
    $subClass = $subClass ?? 'mt-1 text-xs text-slate-600';
    $haritaModu = $haritaModu ?? 'full';
    $showIptal = ! empty($showIptal);
    $gizliRozet = ! empty($gizliRozet);
    $kurumDetay = (bool) ($kurumDetay ?? true);

    $h = $r->doctor?->hospital;
    $lat = null;
    $lng = null;
    if ($h && $h->latitude !== null && $h->latitude !== '' && $h->longitude !== null && $h->longitude !== '') {
        $lat = (float) $h->latitude;
        $lng = (float) $h->longitude;
    }
    $haritaQuery = $h ? trim(implode(' ', array_filter([
        (string) $h->name,
        (string) ($h->address ?? ''),
        (string) ($h->city ?? ''),
        'Türkiye',
    ]))) : '';
    $osmSearch = $haritaQuery !== '' ? 'https://www.openstreetmap.org/search?query='.rawurlencode($haritaQuery) : null;
    $gmapsSearch = $haritaQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($haritaQuery) : null;
    $gmapsDir = ($lat !== null && $lng !== null)
        ? 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($lat.','.$lng)
        : ($gmapsSearch ?? null);

    $bbox = null;
    $embedSrc = null;
    if ($lat !== null && $lng !== null) {
        $d = 0.012;
        $bbox = ($lng - $d).','.($lat - $d).','.($lng + $d).','.($lat + $d);
        $embedSrc = 'https://www.openstreetmap.org/export/embed.html?bbox='.rawurlencode($bbox).'&layer=mapnik&marker='.rawurlencode($lat.','.$lng);
    }
@endphp

<div class="{{ $cardClass }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <div class="{{ $titleClass }}">
                    @if ($r->slot)
                        {{ $r->slot->baslangic->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                    @else
                        —
                    @endif
                </div>
                @if ($gizliRozet)
                    <span class="inline-flex rounded-full bg-violet-200/90 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-violet-950">Gizli</span>
                @endif
            </div>
            <div class="{{ $subClass }}">
                Dr. {{ $r->doctor?->user?->name ?? $r->doctor?->title ?? '—' }}
                @if ($r->doctor?->department) · {{ $r->doctor->department->name }} @endif
            </div>
            @if ($h && $kurumDetay)
                <div class="mt-2 rounded-xl border border-sky-100/90 bg-sky-50/50 px-3 py-2 text-[11px] leading-snug text-slate-700">
                    <div class="font-bold text-slate-900">{{ $h->name }}</div>
                    @if ($h->is_saglik_merkezi)
                        <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-teal-800">Sağlık merkezi</div>
                    @else
                        <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-800">Hastane</div>
                    @endif
                    @if (! empty($h->address))
                        <div class="mt-1 text-slate-600">{{ $h->address }}</div>
                    @endif
                    <div class="mt-1 flex flex-wrap gap-x-2 gap-y-0.5 text-slate-500">
                        @if (! empty($h->city))
                            <span>{{ $h->city }}</span>
                        @endif
                        @if (! empty($h->phone))
                            <span>· {{ $h->phone }}</span>
                        @endif
                    </div>
                </div>
            @elseif ($h && ! $kurumDetay)
                <div class="mt-1 truncate text-[10px] font-semibold text-slate-600">{{ $h->name }}</div>
            @endif
            @if ($r->sikayet && ! $showIptal)
                <div class="mt-2 text-[11px] {{ $notClass ?? 'text-slate-500' }}">
                    Not: {{ \Illuminate\Support\Str::limit($r->sikayet, $sikayetLimit ?? 160) }}
                </div>
            @endif
        </div>
        <span class="inline-flex shrink-0 self-start rounded-full px-2.5 py-1 text-[11px] font-bold {{ $durumEtiket[1] }}">{{ $durumEtiket[0] }}</span>
    </div>

    @if ($h && $haritaModu === 'full')
        <div class="mt-3 border-t border-sky-100/80 pt-3">
            <div class="text-[11px] font-bold text-slate-800">Kurum konumu</div>
            @if ($embedSrc)
                <div class="mt-2 overflow-hidden rounded-xl border border-sky-200/80 bg-white shadow-sm">
                    <iframe title="{{ $h->name }} konumu"
                            class="h-52 w-full"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="{{ $embedSrc }}"></iframe>
                </div>
            @else
                <p class="mt-2 text-[11px] text-slate-600">
                    Bu kurum için haritada göstermek üzere koordinat kaydı yok. Adresle arama yapabilirsiniz.
                </p>
            @endif
            <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold">
                @if ($gmapsDir)
                    <a href="{{ $gmapsDir }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-emerald-200 bg-emerald-50/80 px-2.5 py-1 text-emerald-900 hover:bg-emerald-100/80 transition">Yol tarifi (Google)</a>
                @endif
                @if ($osmSearch)
                    <a href="{{ $osmSearch }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-sky-200 bg-white px-2.5 py-1 text-sky-900 hover:bg-sky-50 transition">OpenStreetMap’te aç</a>
                @endif
            </div>
        </div>
    @elseif ($h && $haritaModu === 'link')
        <div class="mt-1 flex flex-wrap gap-x-2 gap-y-0.5 text-[10px] font-semibold">
            @if ($gmapsDir)
                <a href="{{ $gmapsDir }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:underline">Konum / yol tarifi</a>
            @elseif ($gmapsSearch)
                <a href="{{ $gmapsSearch }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:underline">Haritada ara</a>
            @endif
        </div>
    @endif

    @if ($showIptal)
        <div class="mt-3 flex items-center justify-between gap-3 border-t border-sky-100/80 pt-3">
            <div class="text-[11px] text-slate-500">
                {{ $r->sikayet ? 'Not: '.\Illuminate\Support\Str::limit($r->sikayet, 70) : 'Şikayet yok' }}
            </div>
            <form method="post" action="{{ route('musteri.randevu.iptal', $r) }}" class="inline" onsubmit="return confirm('Randevuyu iptal etmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50/60 transition">
                    İptal
                </button>
            </form>
        </div>
    @endif
</div>

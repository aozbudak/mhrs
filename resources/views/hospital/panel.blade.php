@extends('layouts.hospital')

@section('title', 'Kurum özeti')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wide text-sky-600">Doktor sayısı</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $doktorSayisi }}</p>
            </div>
            <div class="rounded-3xl border border-emerald-100/80 bg-white/70 hospital-glass p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">Aktif randevu</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $aktifRandevu }}</p>
                <p class="mt-1 text-xs text-slate-500">İptal edilmemiş tüm kayıtlar</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route($kr.'.ayarlar') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50/80 px-5 py-2.5 text-sm font-semibold text-sky-900 hover:bg-sky-100/80 transition">
                Çalışma saatleri
            </a>
            <a href="{{ route($kr.'.randevular.index') }}" class="inline-flex items-center rounded-2xl border border-emerald-200 bg-emerald-50/80 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/80 transition">
                Randevuları görüntüle
            </a>
        </div>

        <div id="kurum-doktorlari" class="scroll-mt-24 rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Kurumdaki doktorlar</h2>
                    <p class="mt-1 text-xs text-slate-600">Poliklinik seçerek süzebilirsiniz; liste alfabetiktir.</p>
                </div>
                <a href="{{ route($kr.'.doktorlar', array_filter(['poliklinik' => $seciliPoliklinikId])) }}" class="text-xs font-semibold text-sky-800 underline hover:text-sky-950">Tam ekran liste</a>
            </div>
            @include('hospital.partials.doktorlar-poliklinik-secici', [
                'poliklinikler' => $poliklinikler,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'routeName' => $routeName,
                'doktorToplam' => $doktorSayisi,
            ])
            @include('hospital.partials.doktorlar-tablo', [
                'doktorlar' => $doktorlar,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'poliklinikFiltreTemizRoute' => $poliklinikFiltreTemizRoute,
            ])
        </div>
    </div>
@endsection

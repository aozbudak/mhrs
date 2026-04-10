@extends('layouts.hospital')

@section('title', 'Doktorlar')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <p class="text-sm text-slate-600">Poliklinik seçerek doktorları süzebilirsiniz. Yeni doktor tanımı yalnızca merkez yönetici panelinden yapılır.</p>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Liste</h2>
            @include('hospital.partials.doktorlar-poliklinik-secici', [
                'poliklinikler' => $poliklinikler,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'routeName' => $routeName,
                'doktorToplam' => $doktorToplam,
            ])
            @include('hospital.partials.doktorlar-tablo', [
                'doktorlar' => $doktorlar,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'poliklinikFiltreTemizRoute' => $poliklinikFiltreTemizRoute,
            ])
        </div>
    </div>
@endsection

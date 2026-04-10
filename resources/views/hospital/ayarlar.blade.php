@extends('layouts.hospital')

@section('title', 'Çalışma saatleri')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <p class="text-sm text-slate-600 leading-relaxed">
            Kurum adı, adres ve iletişim bilgileri yalnızca <strong class="font-semibold text-slate-800">merkez yönetici</strong> panelinden düzenlenir. Burada yalnızca günlük çalışma programını güncelleyebilirsiniz.
        </p>

        <form method="post" action="{{ route($kr.'.ayarlar.guncelle') }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-6">
            @csrf
            @method('PUT')
            @if($seciliPoliklinikId)
                <input type="hidden" name="poliklinik" value="{{ $seciliPoliklinikId }}" />
            @endif

            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Günlük çalışma saatleri</h2>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Kayıt sonrası bu kurumdaki doktorlar için bağlı randevusu olmayan gelecekteki boş slotlar yeniden üretilir.
                </p>
                <div class="mt-4">
                    @include('admin.partials.working-hours-intervals', [
                        'intervals' => $intervals,
                        'gunler' => $gunler,
                        'bodyId' => 'hospitalSelfWhBody',
                        'tplId' => 'hospitalSelfWhTpl',
                        'addBtnId' => 'hospitalSelfWhAdd',
                    ])
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-sky-100 pt-4">
                <button type="submit" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
                    Saatleri kaydet
                </button>
                <a href="{{ route($kr.'.panel') }}" class="rounded-2xl border border-sky-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    Özete dön
                </a>
            </div>
        </form>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Kurumdaki doktorlar</h2>
            <p class="text-xs text-slate-600 leading-relaxed">Yeni doktor ekleme ve kayıt düzenleme yalnızca <strong class="font-semibold text-slate-800">merkez yönetici</strong> panelinden yapılır. Poliklinik seçerek listeyi süzebilirsiniz.</p>
            @include('hospital.partials.doktorlar-poliklinik-secici', [
                'poliklinikler' => $poliklinikler,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'routeName' => $routeName,
                'doktorToplam' => $doktorToplam,
            ])
            @include('hospital.partials.doktorlar-tablo', [
                'doktorlar' => $doktorlarFiltreli,
                'seciliPoliklinikId' => $seciliPoliklinikId,
                'poliklinikFiltreTemizRoute' => $poliklinikFiltreTemizRoute,
            ])
        </div>
    </div>
@endsection

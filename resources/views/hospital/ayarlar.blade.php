@extends('layouts.hospital')

@section('title', 'Çalışma saatleri')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <p class="text-sm text-slate-600 leading-relaxed">
            Kurum adı, adres ve iletişim bilgileri yalnızca <strong class="font-semibold text-slate-800">merkez yönetici</strong> panelinden düzenlenir. Burada her birimin kendi muayene saatlerini ve randevu dilimini güncelleyebilirsiniz.
        </p>

        <form method="post" action="{{ route($kr.'.ayarlar.guncelle') }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-6">
            @csrf
            @method('PUT')
            @if($seciliPoliklinikId)
                <input type="hidden" name="poliklinik" value="{{ $seciliPoliklinikId }}" />
            @endif

            @include('admin.partials.poliklinik-muayene-saatleri', [
                'poliklinikSaatleri' => $poliklinikSaatleri ?? [],
                'requiredDeptIdsMuayene' => $requiredDeptIdsMuayene ?? [],
            ])

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

        <div class="rounded-3xl border border-indigo-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Bölüm başkanları</h2>
            <p class="text-xs text-slate-600">Bölüm başkanı ekleme ve silme işlemi kurum panelinden yapılır.</p>

            <div class="space-y-2">
                @forelse($hastane->managedDepartmentHeads as $head)
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-indigo-200/70 bg-indigo-50/40 px-3 py-2">
                        <div class="text-xs text-slate-700">
                            <strong class="text-slate-900">{{ $head->name }}</strong>
                            · {{ $head->managedDepartment?->name ?? 'Birim yok' }}
                            <div class="text-[11px] text-slate-500">{{ $head->email }}</div>
                        </div>
                        <form method="post" action="{{ route('hastane.bolum-baskani.destroy', $head) }}" onsubmit="return confirm('Bölüm başkanı kaldırılsın mı?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                Kaldır
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Henüz bölüm başkanı eklenmemiş.</p>
                @endforelse
            </div>

            <form method="post" action="{{ route('hastane.bolum-baskani.store') }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="bolum_baskani_name" class="text-xs font-bold text-slate-700">Ad soyad</label>
                    <input type="text" name="bolum_baskani_name" id="bolum_baskani_name" value="{{ old('bolum_baskani_name') }}" required
                           class="mt-1 w-full rounded-2xl border border-indigo-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="bolum_baskani_email" class="text-xs font-bold text-slate-700">E-posta</label>
                    <input type="email" name="bolum_baskani_email" id="bolum_baskani_email" value="{{ old('bolum_baskani_email') }}" required
                           class="mt-1 w-full rounded-2xl border border-indigo-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="bolum_baskani_department_id" class="text-xs font-bold text-slate-700">Bölüm</label>
                    <select name="bolum_baskani_department_id" id="bolum_baskani_department_id" required
                            class="mt-1 w-full rounded-2xl border border-indigo-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">
                        <option value="">Seçin</option>
                        @foreach($hastaneBolumleri as $dep)
                            <option value="{{ $dep->id }}" @selected((string) old('bolum_baskani_department_id') === (string) $dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bolum_baskani_password" class="text-xs font-bold text-slate-700">Şifre</label>
                    <input type="password" name="bolum_baskani_password" id="bolum_baskani_password" required
                           class="mt-1 w-full rounded-2xl border border-indigo-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="bolum_baskani_password_confirmation" class="text-xs font-bold text-slate-700">Şifre tekrar</label>
                    <input type="password" name="bolum_baskani_password_confirmation" id="bolum_baskani_password_confirmation" required
                           class="mt-1 w-full rounded-2xl border border-indigo-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl border border-indigo-300 bg-indigo-50 px-5 py-2.5 text-sm font-semibold text-indigo-900 hover:bg-indigo-100 transition">
                        Bölüm başkanı ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

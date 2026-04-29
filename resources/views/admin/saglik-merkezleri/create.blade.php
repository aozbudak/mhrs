@extends('layouts.admin')

@section('title', 'Sağlık merkezi ekle')
@section('subtitle', 'Sağlık merkezi bilgileri ve isteğe bağlı doktor hesapları')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <form method="post" action="{{ route('admin.saglik-merkezleri.store') }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-6">
            @csrf

            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Kurum bilgileri</h2>
                <p class="mt-1 text-xs text-slate-500">Temel iletişim ve konum bilgileri</p>
            </div>

            <div>
                <label for="name" class="text-xs font-bold text-slate-700">Kurum adı</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="text-xs font-bold text-slate-700">Şehir</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="districts_input" class="text-xs font-bold text-slate-700">İlçeler</label>
                    <textarea name="districts_input" id="districts_input" rows="3" placeholder="Örn. Kadıköy&#10;Üsküdar"
                              class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('districts_input') }}</textarea>
                    <p class="mt-1 text-[11px] text-slate-500">Her satıra bir ilçe veya virgülle ayırın.</p>
                </div>
            </div>

            <div>
                <label for="phone" class="text-xs font-bold text-slate-700">Telefon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div>
                <label for="address" class="text-xs font-bold text-slate-700">Adres</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('address') }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="text-xs font-bold text-slate-700">Enlem (latitude)</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" inputmode="decimal" placeholder="Örn. 41.0082"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="longitude" class="text-xs font-bold text-slate-700">Boylam (longitude)</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" inputmode="decimal" placeholder="Örn. 28.9784"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>

            @include('admin.partials.kurum-konum-picker', ['suffix' => 'saglikMerkeziCreate'])

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('is_active', true)) />
                <label for="is_active" class="text-sm font-medium text-slate-800">Aktif</label>
            </div>

            <div class="border-t border-sky-100 pt-6">
                <h2 class="text-sm font-extrabold text-slate-900">Doktorlar (isteğe bağlı)</h2>
                <p class="mt-1 text-xs text-slate-600">Kurum kaydıyla birlikte hesap açmak istediğiniz doktorları ekleyin; daha sonra da düzenleme sayfasından ekleyebilirsiniz.</p>
                <div class="mt-4">
                    @include('admin.partials.hospital-doctor-rows', [
                        'departments' => $departments,
                        'doctorRows' => $doctorRows,
                        'departmentInputMode' => 'text',
                    ])
                </div>
            </div>

            <div class="border-t border-sky-100 pt-6">
                @include('admin.partials.kurum-yoneticisi-create-fields', ['kurumPath' => 'saglik-merkezi'])
            </div>

            <div class="flex flex-wrap gap-2 border-t border-sky-100 pt-4">
                <button type="submit" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
                    Kaydet
                </button>
                <a href="{{ route('admin.saglik-merkezleri.index') }}" class="rounded-2xl border border-sky-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    İptal
                </a>
            </div>
        </form>
    </div>
@endsection

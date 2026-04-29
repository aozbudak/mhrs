@extends('layouts.admin')

@section('title', 'Sa?l?k merkezi düzenle')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <form method="post" action="{{ route('admin.saglik-merkezleri.update', $hastane) }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Kurum bilgileri</h2>
            </div>

            <div>
                <label for="name" class="text-xs font-bold text-slate-700">Kurum ad?</label>
                <input type="text" name="name" id="name" value="{{ old('name', $hastane->name) }}" required
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="text-xs font-bold text-slate-700">ªehir</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $hastane->city) }}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="districts_input" class="text-xs font-bold text-slate-700">?lçeler</label>
                    <textarea name="districts_input" id="districts_input" rows="3" placeholder="Her sat?ra bir ilçe"
                              class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('districts_input', implode("\n", $hastane->districts ?? [])) }}</textarea>
                </div>
            </div>

            <div>
                <label for="phone" class="text-xs font-bold text-slate-700">Telefon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $hastane->phone) }}"
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div>
                <label for="address" class="text-xs font-bold text-slate-700">Adres</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('address', $hastane->address) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="text-xs font-bold text-slate-700">Enlem (latitude)</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $hastane->latitude) }}" inputmode="decimal"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="longitude" class="text-xs font-bold text-slate-700">Boylam (longitude)</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $hastane->longitude) }}" inputmode="decimal"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>

            @include('admin.partials.kurum-konum-picker', ['suffix' => 'saglikMerkeziEdit'])

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('is_active', $hastane->is_active)) />
                <label for="is_active" class="text-sm font-medium text-slate-800">Aktif</label>
            </div>

            @include('admin.partials.poliklinik-muayene-saatleri', [
                'poliklinikSaatleri' => $poliklinikSaatleri ?? [],
                'requiredDeptIdsMuayene' => $requiredDeptIdsMuayene ?? [],
            ])

            <div class="flex flex-wrap gap-2 border-t border-sky-100 pt-4">
                <button type="submit" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
                    Sa?l?k merkezini kaydet
                </button>
                <a href="{{ route('admin.saglik-merkezleri.index') }}" class="rounded-2xl border border-sky-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    Listeye dön
                </a>
            </div>
        </form>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Bu kurumdaki doktorlar</h2>
            <div class="overflow-x-auto rounded-2xl border border-sky-200/60">
                <table class="min-w-full text-sm">
                    <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Doktor</th>
                            <th class="px-3 py-2 text-left">Sa?l?k merkezi</th>
                            <th class="px-3 py-2 text-left">Fiziksel poliklinik / oda</th>
                            <th class="px-3 py-2 text-right">?ºlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-100">
                        @forelse ($hastane->doctors as $d)
                            <tr class="hover:bg-sky-50/40">
                                <td class="px-3 py-2.5 font-medium text-slate-900">{{ $d->user?->name ?? $d->title ?? 'Kay?t #'.$d->id }}</td>
                                <td class="px-3 py-2.5 text-xs text-slate-600">{{ $hastane->name }}</td>
                                <td class="px-3 py-2.5 text-xs text-slate-600">
                                    {{ $d->physical_clinic_name ?? '?' }}
                                    @if($d->room_no)
                                        ? Oda {{ $d->room_no }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    @if ($d->user)
                                        <a href="{{ route('admin.doktorlar.edit', $d) }}" class="rounded-xl border border-sky-200 bg-sky-50/80 px-2.5 py-1 text-[11px] font-semibold text-sky-900 hover:bg-sky-100/80 transition">Düzenle</a>
                                    @else
                                        <span class="text-[11px] text-slate-400">Kullan?c? yok</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">Henüz doktor eklenmemiº.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Bu kuruma doktor ekle</h2>
            <p class="text-xs text-slate-600">Yeni kullan?c? hesab? oluºturulur ve do?rudan bu kuruma atan?r.</p>
            <form method="post" action="{{ route('admin.saglik-merkezleri.doktorlar.store', $hastane) }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="doc_name" class="text-xs font-bold text-slate-700">Ad soyad</label>
                    <input type="text" name="name" id="doc_name" value="{{ old('name') }}" required
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="doc_email" class="text-xs font-bold text-slate-700">E-posta</label>
                    <input type="email" name="email" id="doc_email" value="{{ old('email') }}" required
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_password" class="text-xs font-bold text-slate-700">ªifre</label>
                    <input type="password" name="password" id="doc_password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_password_confirmation" class="text-xs font-bold text-slate-700">ªifre (tekrar)</label>
                    <input type="password" name="password_confirmation" id="doc_password_confirmation" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="doc_department_name" class="text-xs font-bold text-slate-700">Birim</label>
                    <input type="text" name="department_name" id="doc_department_name" value="{{ old('department_name') }}" required
                           placeholder="Orn. Fizik Tedavi ve Rehabilitasyon"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_physical_clinic_name" class="text-xs font-bold text-slate-700">Fiziksel poliklinik (iste?e ba?l?)</label>
                    <input type="text" name="physical_clinic_name" id="doc_physical_clinic_name" value="{{ old('physical_clinic_name') }}" placeholder="?rn. Dahiliye Pol. 2"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_room_no" class="text-xs font-bold text-slate-700">Oda no (iste?e ba?l?)</label>
                    <input type="text" name="room_no" id="doc_room_no" value="{{ old('room_no') }}" placeholder="?rn. A-204"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_title" class="text-xs font-bold text-slate-700">Ünvan</label>
                    <input type="text" name="title" id="doc_title" value="{{ old('title') }}"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_license" class="text-xs font-bold text-slate-700">Sicil / diploma no</label>
                    <input type="text" name="license_number" id="doc_license" value="{{ old('license_number') }}"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_phone" class="text-xs font-bold text-slate-700">Telefon</label>
                    <input type="text" name="phone" id="doc_phone" value="{{ old('phone') }}"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_tc" class="text-xs font-bold text-slate-700">T.C. kimlik (iste?e ba?l?)</label>
                    <input type="text" name="tc_kimlik_no" id="doc_tc" value="{{ old('tc_kimlik_no') }}" maxlength="11" inputmode="numeric"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="doc_bio" class="text-xs font-bold text-slate-700">Biyografi</label>
                    <textarea name="bio" id="doc_bio" rows="2"
                              class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('bio') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="hidden" name="is_aile_hekimi" value="0" />
                    <input type="checkbox" name="is_aile_hekimi" id="doc_is_aile_hekimi" value="1" class="h-4 w-4 rounded border-emerald-300" @checked(old('is_aile_hekimi')) />
                    <label for="doc_is_aile_hekimi" class="text-sm font-medium text-slate-800">Aile hekimi (kay?tl? hastalar bu ünvana göre öneri alabilir)</label>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition">
                        Doktoru ekle
                    </button>
                </div>
            </form>
        </div>

        @include('admin.partials.kurum-yoneticileri-yonet', [
            'hastane' => $hastane,
            'kurumYoneticisiUpdateRoute' => 'admin.saglik-merkezleri.kurum-yoneticisi.update',
            'kurumYoneticisiDestroyRoute' => 'admin.saglik-merkezleri.kurum-yoneticisi.destroy',
        ])

        <div class="rounded-2xl border border-violet-100/80 bg-white/70 hospital-glass p-3 shadow-sm space-y-2">
            <h2 class="text-xs font-extrabold text-slate-900">Yeni kurum paneli kullan?c?s?</h2>
            <p class="text-[10px] text-slate-600 leading-snug">
                Bu sa?l?k merkezi i?in <strong class="font-semibold text-slate-800">/saglik-merkezi</strong> giri?i. ?Kurum? sekmesi.
            </p>
            <form method="post" action="{{ route('admin.saglik-merkezleri.kurum-yoneticisi.store', $hastane) }}" class="grid gap-2 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="kurum_admin_name" class="text-[10px] font-bold text-slate-700">Ad soyad</label>
                    <input type="text" name="kurum_admin_name" id="kurum_admin_name" value="{{ old('kurum_admin_name') }}" required autocomplete="name"
                           class="mt-0.5 w-full rounded-lg border border-violet-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div class="sm:col-span-2">
                    <label for="kurum_admin_email" class="text-[10px] font-bold text-slate-700">E-posta</label>
                    <input type="email" name="kurum_admin_email" id="kurum_admin_email" value="{{ old('kurum_admin_email') }}" required autocomplete="email"
                           class="mt-0.5 w-full rounded-lg border border-violet-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label for="kurum_admin_password" class="text-[10px] font-bold text-slate-700">?ifre</label>
                    <input type="password" name="kurum_admin_password" id="kurum_admin_password" required autocomplete="new-password"
                           class="mt-0.5 w-full rounded-lg border border-violet-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label for="kurum_admin_password_confirmation" class="text-[10px] font-bold text-slate-700">?ifre (tekrar)</label>
                    <input type="password" name="kurum_admin_password_confirmation" id="kurum_admin_password_confirmation" required autocomplete="new-password"
                           class="mt-0.5 w-full rounded-lg border border-violet-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-lg border border-violet-300 bg-violet-50/90 px-3 py-1.5 text-xs font-semibold text-violet-950 hover:bg-violet-100/90 transition">
                        Kurum paneli hesab? olu?tur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

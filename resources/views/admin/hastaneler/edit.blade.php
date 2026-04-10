@extends('layouts.admin')

@section('title', 'Hastane düzenle')
@section('subtitle', $hastane->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <form method="post" action="{{ route('admin.hastaneler.update', $hastane) }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Hastane bilgileri</h2>
            </div>

            <div>
                <label for="name" class="text-xs font-bold text-slate-700">Hastane adı</label>
                <input type="text" name="name" id="name" value="{{ old('name', $hastane->name) }}" required
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="text-xs font-bold text-slate-700">Şehir</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $hastane->city) }}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="districts_input" class="text-xs font-bold text-slate-700">İlçeler</label>
                    <textarea name="districts_input" id="districts_input" rows="3" placeholder="Her satıra bir ilçe"
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

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('is_active', $hastane->is_active)) />
                <label for="is_active" class="text-sm font-medium text-slate-800">Aktif</label>
            </div>

            <div class="border-t border-sky-100 pt-6">
                <h2 class="text-sm font-extrabold text-slate-900">Hastane çalışma saatleri</h2>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Değişiklik kaydedildiğinde bu hastanedeki tüm doktorlar için bağlı randevusu olmayan gelecekteki boş slotlar silinir ve yeni programa göre yeniden üretilir.
                </p>
                <div class="mt-4">
                    @include('admin.partials.working-hours-intervals', [
                        'intervals' => $intervals,
                        'gunler' => $gunler,
                        'bodyId' => 'hospitalEditWhBody',
                        'tplId' => 'hospitalEditWhTpl',
                        'addBtnId' => 'hospitalEditWhAdd',
                    ])
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-sky-100 pt-4">
                <button type="submit" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
                    Hastaneyi kaydet
                </button>
                <a href="{{ route('admin.hastaneler.index') }}" class="rounded-2xl border border-sky-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    Listeye dön
                </a>
            </div>
        </form>

        <div class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Bu hastanedeki doktorlar</h2>
            <div class="overflow-x-auto rounded-2xl border border-sky-200/60">
                <table class="min-w-full text-sm">
                    <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Doktor</th>
                            <th class="px-3 py-2 text-left">Birim</th>
                            <th class="px-3 py-2 text-left">Fiziksel poliklinik / oda</th>
                            <th class="px-3 py-2 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-100">
                        @forelse ($hastane->doctors as $d)
                            <tr class="hover:bg-sky-50/40">
                                <td class="px-3 py-2.5 font-medium text-slate-900">{{ $d->user?->name ?? $d->title ?? 'Kayıt #'.$d->id }}</td>
                                <td class="px-3 py-2.5 text-xs text-slate-600">{{ $d->department?->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-xs text-slate-600">
                                    {{ $d->physical_clinic_name ?? '—' }}
                                    @if($d->room_no)
                                        · Oda {{ $d->room_no }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    @if ($d->user)
                                        <a href="{{ route('admin.doktorlar.edit', $d) }}" class="rounded-xl border border-sky-200 bg-sky-50/80 px-2.5 py-1 text-[11px] font-semibold text-sky-900 hover:bg-sky-100/80 transition">Düzenle</a>
                                    @else
                                        <span class="text-[11px] text-slate-400">Kullanıcı yok</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">Henüz doktor eklenmemiş.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Bu hastaneye doktor ekle</h2>
            <p class="text-xs text-slate-600">Yeni kullanıcı hesabı oluşturulur ve doğrudan bu hastaneye atanır.</p>
            <form method="post" action="{{ route('admin.hastaneler.doktorlar.store', $hastane) }}" class="grid gap-4 sm:grid-cols-2">
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
                    <label for="doc_password" class="text-xs font-bold text-slate-700">Şifre</label>
                    <input type="password" name="password" id="doc_password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_password_confirmation" class="text-xs font-bold text-slate-700">Şifre (tekrar)</label>
                    <input type="password" name="password_confirmation" id="doc_password_confirmation" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="doc_department" class="text-xs font-bold text-slate-700">Birim</label>
                    <select name="department_id" id="doc_department" required
                            class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">
                        <option value="">Seçin</option>
                        @foreach ($departments as $dep)
                            <option value="{{ $dep->id }}" @selected((string) old('department_id') === (string) $dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="doc_physical_clinic_name" class="text-xs font-bold text-slate-700">Fiziksel poliklinik (isteğe bağlı)</label>
                    <input type="text" name="physical_clinic_name" id="doc_physical_clinic_name" value="{{ old('physical_clinic_name') }}" placeholder="Örn. Dahiliye Pol. 2"
                           class="mt-1 w-full rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="doc_room_no" class="text-xs font-bold text-slate-700">Oda no (isteğe bağlı)</label>
                    <input type="text" name="room_no" id="doc_room_no" value="{{ old('room_no') }}" placeholder="Örn. A-204"
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
                    <label for="doc_tc" class="text-xs font-bold text-slate-700">T.C. kimlik (isteğe bağlı)</label>
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
                    <label for="doc_is_aile_hekimi" class="text-sm font-medium text-slate-800">Aile hekimi (kayıtlı hastalar bu ünvana göre öneri alabilir)</label>
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
            'kurumYoneticisiUpdateRoute' => 'admin.hastaneler.kurum-yoneticisi.update',
        ])

        <div class="rounded-3xl border border-violet-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Yeni kurum paneli kullanıcısı</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Bu hastane için <strong class="font-semibold text-slate-800">/hastane</strong> adresinden giriş yapabilecek bir hesap oluşturur. Giriş sayfasında «Kurum» sekmesini seçmelidir.
            </p>
            <form method="post" action="{{ route('admin.hastaneler.kurum-yoneticisi.store', $hastane) }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="kurum_admin_name" class="text-xs font-bold text-slate-700">Ad soyad</label>
                    <input type="text" name="kurum_admin_name" id="kurum_admin_name" value="{{ old('kurum_admin_name') }}" required autocomplete="name"
                           class="mt-1 w-full rounded-2xl border border-violet-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <label for="kurum_admin_email" class="text-xs font-bold text-slate-700">E-posta</label>
                    <input type="email" name="kurum_admin_email" id="kurum_admin_email" value="{{ old('kurum_admin_email') }}" required autocomplete="email"
                           class="mt-1 w-full rounded-2xl border border-violet-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="kurum_admin_password" class="text-xs font-bold text-slate-700">Şifre</label>
                    <input type="password" name="kurum_admin_password" id="kurum_admin_password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-violet-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="kurum_admin_password_confirmation" class="text-xs font-bold text-slate-700">Şifre (tekrar)</label>
                    <input type="password" name="kurum_admin_password_confirmation" id="kurum_admin_password_confirmation" required autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-violet-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl border border-violet-300 bg-violet-50/90 px-5 py-2.5 text-sm font-semibold text-violet-950 hover:bg-violet-100/90 transition">
                        Kurum paneli hesabı oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

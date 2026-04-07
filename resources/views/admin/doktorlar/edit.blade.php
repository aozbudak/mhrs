@extends('layouts.admin')

@section('title', 'Doktor düzenle')
@section('subtitle', 'Hesap ve birim bilgilerini güncelleyin')

@section('content')
    <div class="mx-auto max-w-2xl">

        @if($departments->isEmpty())
            <div class="rounded-3xl border border-amber-200 bg-amber-50/90 p-4 text-sm text-amber-950">
                Güncelleme için en az bir birim tanımlı olmalı.
            </div>
        @endif

        <form method="post" action="{{ route('admin.doktorlar.update', $doktor) }}" class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="text-xs font-bold text-slate-700">Ad soyad</label>
                <input type="text" name="name" id="name" value="{{ old('name', $doktor->user->name) }}" required
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div>
                <label for="email" class="text-xs font-bold text-slate-700">E-posta</label>
                <input type="email" name="email" id="email" value="{{ old('email', $doktor->user->email) }}" required autocomplete="username"
                       class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="text-xs font-bold text-slate-700">Yeni şifre (isteğe bağlı)</label>
                    <input type="password" name="password" id="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="password_confirmation" class="text-xs font-bold text-slate-700">Şifre tekrar</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>
            <p class="text-[11px] text-slate-500 -mt-2">Şifreyi değiştirmek istemiyorsanız bu alanları boş bırakın.</p>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="tc_kimlik_no" class="text-xs font-bold text-slate-700">T.C. kimlik no (11 hane, isteğe bağlı)</label>
                    <input type="text" name="tc_kimlik_no" id="tc_kimlik_no" value="{{ old('tc_kimlik_no', $doktor->user->tc_kimlik_no) }}" maxlength="11" pattern="[0-9]{11}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                    <p class="mt-1 text-[11px] text-slate-500">Boş bırakılırsa mevcut numara korunur; yoksa sistem üretir.</p>
                </div>
                <div>
                    <label for="phone" class="text-xs font-bold text-slate-700">Telefon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $doktor->user->phone) }}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>

            <div>
                <label for="department_id" class="text-xs font-bold text-slate-700">Birim</label>
                <select name="department_id" id="department_id" required
                        class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">
                    <option value="">Seçin</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected((string) old('department_id', $doktor->department_id) === (string) $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="hospital_id" class="text-xs font-bold text-slate-700">Hastane</label>
                <select name="hospital_id" id="hospital_id" required
                        class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">
                    <option value="">Seçin</option>
                    @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected((string) old('hospital_id', $doktor->hospital_id) === (string) $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-slate-500">Müsait randevu saatleri seçilen hastanenin çalışma programına göre üretilir.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="physical_clinic_name" class="text-xs font-bold text-slate-700">Fiziksel poliklinik</label>
                    <input type="text" name="physical_clinic_name" id="physical_clinic_name" value="{{ old('physical_clinic_name', $doktor->physical_clinic_name) }}" placeholder="Örn. Dahiliye Pol. 2"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="room_no" class="text-xs font-bold text-slate-700">Oda no</label>
                    <input type="text" name="room_no" id="room_no" value="{{ old('room_no', $doktor->room_no) }}" placeholder="Örn. A-204"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="title" class="text-xs font-bold text-slate-700">Ünvan</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $doktor->title) }}" placeholder="Örn. Uzman Dr."
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label for="license_number" class="text-xs font-bold text-slate-700">Sicil / diploma no</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doktor->license_number) }}"
                           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
            </div>

            <div>
                <label for="bio" class="text-xs font-bold text-slate-700">Özgeçmiş (isteğe bağlı)</label>
                <textarea name="bio" id="bio" rows="3"
                          class="mt-1 w-full rounded-2xl border border-sky-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm">{{ old('bio', $doktor->bio) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('is_active', $doktor->is_active)) />
                <label for="is_active" class="text-sm font-medium text-slate-800">Randevu almaya açık (aktif)</label>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_aile_hekimi" value="0" />
                <input type="checkbox" name="is_aile_hekimi" id="is_aile_hekimi" value="1" class="h-4 w-4 rounded border-sky-300" @checked(old('is_aile_hekimi', $doktor->is_aile_hekimi)) />
                <label for="is_aile_hekimi" class="text-sm font-medium text-slate-800">Aile hekimi</label>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="submit" @disabled($departments->isEmpty()) class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-5 py-2.5 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition disabled:cursor-not-allowed disabled:opacity-50">
                    Kaydet
                </button>
                <a href="{{ route('admin.doktorlar.index') }}" class="rounded-2xl border border-sky-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-sky-50/60 transition">
                    İptal
                </a>
            </div>
        </form>
    </div>
@endsection

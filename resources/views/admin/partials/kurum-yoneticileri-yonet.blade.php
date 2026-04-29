@php
    /** @var \App\Models\Hospital $hastane */
    /** @var string $kurumYoneticisiUpdateRoute */
    /** @var string $kurumYoneticisiDestroyRoute */
@endphp

<div class="rounded-2xl border border-slate-200/90 bg-white/80 hospital-glass p-3 shadow-sm space-y-2">
    <h2 class="text-xs font-extrabold text-slate-900">Kurum yöneticileri</h2>
    <p class="text-[10px] leading-snug text-slate-600">
        <strong class="font-semibold text-slate-800">Kurum</strong> girişi. Şifre alanlarını boş bırakırsanız mevcut şifre korunur.
    </p>

    @forelse ($hastane->managedHospitalAdmins as $admin)
        @php
            $aid = (int) $admin->id;
        @endphp
        <div class="rounded-xl border border-slate-200/70 bg-slate-50/50 p-2.5">
            <div class="mb-2 flex flex-wrap items-baseline justify-between gap-1.5 border-b border-slate-200/60 pb-1.5">
                <span class="text-[10px] font-bold text-slate-600">#{{ $aid }}</span>
                <span class="max-w-[65%] truncate text-[10px] text-slate-500">{{ $admin->email }}</span>
            </div>
            <form method="post" action="{{ route($kurumYoneticisiUpdateRoute, [$hastane, $admin]) }}" class="grid gap-2 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-slate-600" for="ka_name_{{ $aid }}">Ad soyad</label>
                    <input type="text" name="kurum_admins[{{ $aid }}][name]" id="ka_name_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.name', $admin->name) }}" required autocomplete="name"
                           class="mt-0.5 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-600" for="ka_email_{{ $aid }}">E-posta</label>
                    <input type="email" name="kurum_admins[{{ $aid }}][email]" id="ka_email_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.email', $admin->email) }}" required autocomplete="email"
                           class="mt-0.5 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-600" for="ka_phone_{{ $aid }}">Telefon</label>
                    <input type="text" name="kurum_admins[{{ $aid }}][phone]" id="ka_phone_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.phone', $admin->phone) }}" autocomplete="tel"
                           class="mt-0.5 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-600" for="ka_pw_{{ $aid }}">Yeni şifre</label>
                    <input type="password" name="kurum_admins[{{ $aid }}][password]" id="ka_pw_{{ $aid }}" autocomplete="new-password"
                           class="mt-0.5 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-600" for="ka_pwc_{{ $aid }}">Şifre tekrar</label>
                    <input type="password" name="kurum_admins[{{ $aid }}][password_confirmation]" id="ka_pwc_{{ $aid }}" autocomplete="new-password"
                           class="mt-0.5 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-900" />
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 transition sm:w-auto">
                        Kaydet
                    </button>
                </div>
            </form>
            <form method="post" action="{{ route($kurumYoneticisiDestroyRoute, [$hastane, $admin]) }}" onsubmit="return confirm('Bu kurum yöneticisini silmek istediğinize emin misiniz?');" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-800 hover:bg-rose-100 transition sm:w-auto">
                    Sil
                </button>
            </form>
        </div>
    @empty
        <p class="text-xs text-slate-500">Henüz tanımlı kurum yöneticisi yok.</p>
    @endforelse
</div>

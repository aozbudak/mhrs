@php
    /** @var \App\Models\Hospital $hastane */
    /** @var string $kurumYoneticisiUpdateRoute */
@endphp

<div class="rounded-3xl border border-slate-200/90 bg-white/70 hospital-glass p-5 shadow-sm space-y-4">
    <h2 class="text-sm font-extrabold text-slate-900">Kurum yöneticileri</h2>
    <p class="text-xs text-slate-600 leading-relaxed">
        Bu kuruma atanmış <strong class="font-semibold text-slate-800">Kurum</strong> girişi yapan hesapları buradan güncelleyebilirsiniz. Şifre alanlarını boş bırakırsanız mevcut şifre korunur.
    </p>

    @forelse ($hastane->managedHospitalAdmins as $admin)
        @php
            $aid = (int) $admin->id;
        @endphp
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/40 p-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div class="text-xs font-bold text-slate-700">Hesap #{{ $aid }}</div>
                <div class="truncate text-[11px] text-slate-500">{{ $admin->email }}</div>
            </div>
            <form method="post" action="{{ route($kurumYoneticisiUpdateRoute, [$hastane, $admin]) }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <label class="text-xs font-bold text-slate-700" for="ka_name_{{ $aid }}">Ad soyad</label>
                    <input type="text" name="kurum_admins[{{ $aid }}][name]" id="ka_name_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.name', $admin->name) }}" required autocomplete="name"
                           class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700" for="ka_email_{{ $aid }}">E-posta</label>
                    <input type="email" name="kurum_admins[{{ $aid }}][email]" id="ka_email_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.email', $admin->email) }}" required autocomplete="email"
                           class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700" for="ka_phone_{{ $aid }}">Telefon</label>
                    <input type="text" name="kurum_admins[{{ $aid }}][phone]" id="ka_phone_{{ $aid }}"
                           value="{{ old('kurum_admins.'.$aid.'.phone', $admin->phone) }}" autocomplete="tel"
                           class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700" for="ka_pw_{{ $aid }}">Yeni şifre (isteğe bağlı)</label>
                    <input type="password" name="kurum_admins[{{ $aid }}][password]" id="ka_pw_{{ $aid }}" autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-700" for="ka_pwc_{{ $aid }}">Yeni şifre (tekrar)</label>
                    <input type="password" name="kurum_admins[{{ $aid }}][password_confirmation]" id="ka_pwc_{{ $aid }}" autocomplete="new-password"
                           class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm" />
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-900 transition">
                        Bu yöneticiyi kaydet
                    </button>
                </div>
            </form>
        </div>
    @empty
        <p class="text-sm text-slate-500">Henüz tanımlı kurum yöneticisi yok. Aşağıdan yeni hesap oluşturabilirsiniz.</p>
    @endforelse
</div>

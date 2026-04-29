{{-- İsteğe bağlı: kurum oluştururken aynı anda kurum paneli yöneticisi --}}
@php
    $kurumPath = $kurumPath ?? 'hastane';
@endphp
<div class="rounded-2xl border border-violet-200/80 bg-violet-50/30 p-4 space-y-3">
    <div>
        <h2 class="text-xs font-extrabold text-violet-950">Kurum yöneticisi (isteğe bağlı)</h2>
        <p class="mt-1 text-[11px] leading-relaxed text-violet-900/80">
            Doldurursanız kayıt ile birlikte <strong class="font-semibold">{{ $kurumPath === 'saglik-merkezi' ? '/saglik-merkezi' : '/hastane' }}</strong> panelinden giriş yapacak bir hesap oluşturulur. Boş bırakırsanız daha sonra düzenleme sayfasından ekleyebilirsiniz.
        </p>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="kurum_admin_name" class="text-[11px] font-bold text-slate-700">Ad soyad</label>
            <input type="text" name="kurum_admin_name" id="kurum_admin_name" value="{{ old('kurum_admin_name') }}" autocomplete="name"
                   class="mt-1 w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm" />
        </div>
        <div class="sm:col-span-2">
            <label for="kurum_admin_email" class="text-[11px] font-bold text-slate-700">E-posta</label>
            <input type="email" name="kurum_admin_email" id="kurum_admin_email" value="{{ old('kurum_admin_email') }}" autocomplete="email"
                   class="mt-1 w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm" />
        </div>
        <div>
            <label for="kurum_admin_password" class="text-[11px] font-bold text-slate-700">Şifre</label>
            <input type="password" name="kurum_admin_password" id="kurum_admin_password" autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm" />
        </div>
        <div>
            <label for="kurum_admin_password_confirmation" class="text-[11px] font-bold text-slate-700">Şifre (tekrar)</label>
            <input type="password" name="kurum_admin_password_confirmation" id="kurum_admin_password_confirmation" autocomplete="new-password"
                   class="mt-1 w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm" />
        </div>
    </div>
</div>

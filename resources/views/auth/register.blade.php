@extends('layouts.auth')

@section('title', 'Kayıt')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 md:items-stretch">
        <!-- Left medical illustration -->
        <aside class="hidden md:block">
            <div class="h-full rounded-3xl hospital-glass border border-sky-100/80 bg-white/60 p-8 shadow-sm">
                <div class="flex items-center gap-2 rounded-full border border-sky-200/70 bg-sky-50/60 px-4 py-2 text-sm font-semibold text-sky-900">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-sky-200 bg-white/60">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10 2.2C6.7 2.2 4 4.8 4 8C4 12.4 10 17.8 10 17.8C10 17.8 16 12.4 16 8C16 4.8 13.3 2.2 10 2.2Z" stroke="#0ea5e9" stroke-width="2" />
                            <path d="M7.6 8.1L9.2 9.7L12.4 6.5" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    Güvenli Üyelik
                </div>

                <h1 class="mt-6 text-3xl font-extrabold leading-tight text-sky-950">Hasta kaydı</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    Adım adım; güvenli ve sakin bir kayıt deneyimi.
                </p>

                <div class="mt-8 grid gap-4">
                    <div class="rounded-2xl border border-sky-100 bg-white/60 p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-sky-200 bg-sky-50/70">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Temiz form</div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    Sadece gerekli alanlar. İkonlarla destekli.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50/70 to-emerald-50/60 p-4">
                        <div class="text-sm font-semibold text-sky-950">Gizlilik odaklı</div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            Şifreler güvenli şekilde saklanır (backend mevcut güvenli kurallarıyla).
                        </div>
                    </div>
                </div>

                <div class="mt-7 rounded-2xl border border-sky-100 bg-white/60 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-sky-50 to-emerald-50 border border-sky-200">
                            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M20 26C20 18 26 12 34 12C42 12 48 18 48 26C48 38 34 52 34 52C34 52 20 38 20 26Z" stroke="#0ea5e9" stroke-width="3"/>
                                <path d="M28 26H40" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                                <path d="M34 20V32" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Sade & profesyonel</div>
                            <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                Hastane arayüzü hissi; sakin renk paleti ve yuvarlatılmış elemanlar.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right register form -->
        <section class="rounded-3xl border border-sky-100/80 bg-white/70 hospital-glass p-7 shadow-sm md:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-sky-950">Kayıt</h1>
                    <p class="mt-1 text-sm text-slate-600">Hasta hesabınızı oluşturun.</p>
                </div>
                <div class="hidden sm:block rounded-2xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-800">
                    Sağlık kalitesi
                </div>
            </div>

            <form method="post" action="{{ route('register') }}" class="mt-6 space-y-4" aria-label="Kayıt formu">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Ad</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                autocomplete="given-name"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="surname" class="mb-1 block text-sm font-medium text-slate-700">Soyad</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M10 3.2C6.9 3.2 4.4 5.7 4.4 8.8C4.4 11.9 6.9 14.4 10 14.4C13.1 14.4 15.6 11.9 15.6 8.8C15.6 5.7 13.1 3.2 10 3.2Z" stroke="#0ea5e9" stroke-width="2" />
                                    <path d="M3 18.1C4.3 15.7 6.8 14.2 10 14.2C13.2 14.2 15.7 15.7 17 18.1" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="surname"
                                id="surname"
                                value="{{ old('surname') }}"
                                required
                                autocomplete="family-name"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tc_kimlik_no" class="mb-1 block text-sm font-medium text-slate-700">T.C. kimlik no</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="4" y="3" width="12" height="14" rx="2" stroke="#0ea5e9" stroke-width="1.8"/>
                                <path d="M7 8H13M7 11H13M7 14H10" stroke="#10b981" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="tc_kimlik_no"
                            id="tc_kimlik_no"
                            value="{{ old('tc_kimlik_no') }}"
                            required
                            inputmode="numeric"
                            maxlength="11"
                            autocomplete="off"
                            class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            placeholder="11 haneli T.C. kimlik numaranız"
                        >
                    </div>
                    @error('tc_kimlik_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="birth_date" class="mb-1 block text-sm font-medium text-slate-700">Doğum tarihi <span class="text-rose-600">*</span></label>
                    <input
                        type="date"
                        name="birth_date"
                        id="birth_date"
                        value="{{ old('birth_date') }}"
                        required
                        max="{{ now()->format('Y-m-d') }}"
                        class="w-full rounded-2xl border border-sky-200 bg-white/80 px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                    >
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p id="oncelik_yas_uyari" class="mt-1 hidden text-xs font-medium text-emerald-800">Doğum tarihinize göre {{ \App\Models\User::ONCELIKLI_YAS_ESIGI }} yaş üstü olduğunuz için öncelikli hasta statünüz otomatik uygulanır.</p>
                    <p class="mt-1 text-xs text-slate-500">18 yaşından küçük hastalar için veli T.C. alanı açılır; nüfus kaydıyla uyumlu olmalıdır. Öncelikli hasta bilgisi kayıtla sabittir, sonra değiştirilemez.</p>
                </div>

                <fieldset class="rounded-2xl border border-sky-200 bg-white/60 px-4 py-3">
                    <legend class="px-1 text-sm font-semibold text-slate-800">Engel durumu <span class="text-rose-600">*</span></legend>
                    <p class="mt-1 text-xs text-slate-600">Engelli hastaysanız öncelikli randevu slotlarına erişebilirsiniz. Bu seçim kayıt sonrası değiştirilemez; lütfen doğru beyan edin.</p>
                    <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-800">
                        <label class="inline-flex cursor-pointer items-center gap-2">
                            <input type="radio" name="engelli" value="0" class="h-4 w-4 border-sky-300 text-emerald-600 focus:ring-emerald-500" @checked(old('engelli', '0') === '0' || old('engelli', '0') === 0) required>
                            Hayır
                        </label>
                        <label class="inline-flex cursor-pointer items-center gap-2">
                            <input type="radio" name="engelli" value="1" class="h-4 w-4 border-sky-300 text-emerald-600 focus:ring-emerald-500" @checked(old('engelli') === '1' || old('engelli') === 1)>
                            Evet, engelli hastayım
                        </label>
                    </div>
                    @error('engelli')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div id="veli_tc_wrap" class="hidden space-y-1">
                    <label for="veli_tc_kimlik_no" class="mb-1 block text-sm font-medium text-slate-700">Veli / vasi T.C. kimlik no <span id="veli_required_badge" class="hidden text-rose-600">*</span></label>
                    <input
                        type="text"
                        name="veli_tc_kimlik_no"
                        id="veli_tc_kimlik_no"
                        value="{{ old('veli_tc_kimlik_no') }}"
                        inputmode="numeric"
                        maxlength="11"
                        autocomplete="off"
                        placeholder="11 hane — veli hesabı sistemde kayıtlı olmalı"
                        class="w-full rounded-2xl border border-sky-200 bg-white/80 px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                    >
                    @error('veli_tc_kimlik_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">E-posta</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M3.5 5.5H16.5V14.5H3.5V5.5Z" stroke="#0ea5e9" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M3.8 6.1L10 10.6L16.2 6.1" stroke="#10b981" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                        >
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Şifre</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.2 9.1V6.7C6.2 4.9 7.7 3.4 9.5 3.4C11.3 3.4 12.8 4.9 12.8 6.7V9.1" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M5.4 9.1H13.6C14.3 9.1 14.9 9.7 14.9 10.4V15.1C14.9 15.8 14.3 16.4 13.6 16.4H5.4C4.7 16.4 4.1 15.8 4.1 15.1V10.4C4.1 9.7 4.7 9.1 5.4 9.1Z" stroke="#10b981" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Şifre tekrar</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sky-600">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.2 9.1V6.7C6.2 4.9 7.7 3.4 9.5 3.4C11.3 3.4 12.8 4.9 12.8 6.7V9.1" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M5.4 9.1H13.6C14.3 9.1 14.9 9.7 14.9 10.4V15.1C14.9 15.8 14.3 16.4 13.6 16.4H5.4C4.7 16.4 4.1 15.8 4.1 15.1V10.4C4.1 9.7 4.7 9.1 5.4 9.1Z" stroke="#10b981" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-sky-200 bg-white/80 pl-11 pr-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15"
                            >
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-4 space-y-4">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="sec_aile_hekimi" value="1" id="sec_aile_hekimi" class="mt-1 h-4 w-4 rounded border-sky-300"
                               @checked(old('sec_aile_hekimi'))>
                        <span class="text-sm text-slate-800">
                            <span class="font-semibold">Aile hekimi seçmek istiyorum</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-600">Kayıtta girdiğiniz il ve ilçeye göre hastane konumları kullanılarak en yakın aile hekimleri sıralanır.</span>
                        </span>
                    </label>

                    <div id="aile-hekim-fields" class="space-y-3 @unless(old('sec_aile_hekimi')) hidden @endunless">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="patient_city" class="mb-1 block text-sm font-medium text-slate-700">İl</label>
                                <select name="patient_city" id="patient_city"
                                        class="w-full rounded-2xl border border-sky-200 bg-white/80 px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                                    <option value="">İl seçin</option>
                                    @foreach ($cities as $c)
                                        <option value="{{ $c }}" @selected(old('patient_city') === $c)>{{ $c }}</option>
                                    @endforeach
                                </select>
                                @error('patient_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div id="patient_district_wrap">
                                <label for="patient_district" class="mb-1 block text-sm font-medium text-slate-700">İlçe</label>
                                <select name="patient_district" id="patient_district"
                                        class="w-full rounded-2xl border border-sky-200 bg-white/80 px-4 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15">
                                    <option value="">Önce il seçin</option>
                                </select>
                                <p id="patient_district_hint" class="mt-1 hidden text-xs text-slate-500">Bu il için ilçe listesi yok; öneriler il geneline göre yapılır.</p>
                                @error('patient_district')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Yakın aile hekimleri</p>
                            <div id="aile-hekim-list" class="min-h-[3rem] rounded-2xl border border-dashed border-sky-200 bg-white/60 px-3 py-3 text-sm text-slate-600">
                                İl (ve varsa ilçe) seçtikten sonra liste burada görünür.
                            </div>
                            @error('aile_hekimi_doctor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition"
                >
                    Kayıt ol
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                Zaten hesabınız var mı?
                <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:text-emerald-900">Giriş yapın</a>
            </p>
        </section>
    </div>

    <script>
        (function () {
            var routes = {
                ilceler: @json(route('register.ilceler')),
                aileHekimleri: @json(route('register.aile-hekimleri'))
            };
            var oldCity = @json(old('patient_city'));
            var oldDistrict = @json(old('patient_district'));
            var oldDoctorId = @json(old('aile_hekimi_doctor_id'));

            var chk = document.getElementById('sec_aile_hekimi');
            var fields = document.getElementById('aile-hekim-fields');
            var selCity = document.getElementById('patient_city');
            var selDistrict = document.getElementById('patient_district');
            var districtHint = document.getElementById('patient_district_hint');
            var districtWrap = document.getElementById('patient_district_wrap');
            var listEl = document.getElementById('aile-hekim-list');
            var form = chk.closest('form');

            if (!chk || !fields || !selCity || !selDistrict || !listEl || !form) return;

            form.addEventListener('submit', function () {
                if (!chk.checked) {
                    selCity.disabled = true;
                    selDistrict.disabled = true;
                    listEl.querySelectorAll('input[name="aile_hekimi_doctor_id"]').forEach(function (inp) {
                        inp.disabled = true;
                    });
                }
            });

            function toggleFields() {
                if (chk.checked) {
                    fields.classList.remove('hidden');
                } else {
                    fields.classList.add('hidden');
                }
            }

            function setListLoading() {
                listEl.textContent = 'Yükleniyor…';
            }

            function setListMessage(msg) {
                listEl.textContent = msg;
            }

            function renderDoctors(rows) {
                listEl.innerHTML = '';
                if (!rows || !rows.length) {
                    setListMessage('Bu kriterlere uygun kayıtlı aile hekimi bulunamadı.');
                    return;
                }
                var ul = document.createElement('div');
                ul.className = 'space-y-2';
                rows.forEach(function (d) {
                    var label = document.createElement('label');
                    label.className = 'flex cursor-pointer items-start gap-3 rounded-xl border border-sky-100 bg-white/80 px-3 py-2.5 hover:border-emerald-200';
                    var input = document.createElement('input');
                    input.type = 'radio';
                    input.name = 'aile_hekimi_doctor_id';
                    input.value = String(d.id);
                    input.className = 'mt-1 h-4 w-4 border-sky-300 text-emerald-600 focus:ring-emerald-500';
                    if (oldDoctorId && String(oldDoctorId) === String(d.id)) {
                        input.checked = true;
                    }
                    var span = document.createElement('span');
                    span.className = 'text-sm text-slate-800';
                    var distPart = d.distance_km != null ? ' · yaklaşık ' + d.distance_km + ' km' : '';
                    span.innerHTML = '<span class="font-semibold">' + escapeHtml(d.name) + '</span>' +
                        '<span class="block text-xs text-slate-600">' + escapeHtml(d.hospital_name) + distPart + '</span>';
                    label.appendChild(input);
                    label.appendChild(span);
                    ul.appendChild(label);
                });
                listEl.appendChild(ul);
            }

            function escapeHtml(s) {
                var div = document.createElement('div');
                div.textContent = s;
                return div.innerHTML;
            }

            function loadDistricts() {
                var city = selCity.value;
                selDistrict.innerHTML = '';
                districtHint.classList.add('hidden');
                selDistrict.disabled = !city;
                if (!city) {
                    var o0 = document.createElement('option');
                    o0.value = '';
                    o0.textContent = 'Önce il seçin';
                    selDistrict.appendChild(o0);
                    setListMessage('İl seçtikten sonra ilçe ve aile hekimi listesi yüklenir.');
                    return;
                }
                fetch(routes.ilceler + '?city=' + encodeURIComponent(city), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                    .then(function (x) {
                        if (!x.ok) {
                            selDistrict.appendChild(new Option('—', ''));
                            return;
                        }
                        var districts = x.j.districts || [];
                        if (!districts.length) {
                            districtHint.classList.remove('hidden');
                            selDistrict.disabled = true;
                            var optAll = document.createElement('option');
                            optAll.value = '';
                            optAll.textContent = 'İl geneli';
                            selDistrict.appendChild(optAll);
                            loadAileHekimleri();
                            return;
                        }
                        selDistrict.disabled = false;
                        selDistrict.appendChild(new Option('İlçe seçin', ''));
                        districts.forEach(function (d) {
                            var opt = document.createElement('option');
                            opt.value = d;
                            opt.textContent = d;
                            if (oldDistrict && oldDistrict === d) opt.selected = true;
                            selDistrict.appendChild(opt);
                        });
                        if (selDistrict.value) {
                            loadAileHekimleri();
                        } else {
                            setListMessage('İlçe seçtikten sonra liste yüklenir.');
                        }
                    })
                    .catch(function () {
                        setListMessage('İlçe listesi yüklenemedi.');
                    });
            }

            function loadAileHekimleri() {
                var city = selCity.value;
                if (!city) return;
                var district = selDistrict.disabled ? '' : selDistrict.value;
                if (!selDistrict.disabled && !district) {
                    setListMessage('İlçe seçtikten sonra liste yüklenir.');
                    return;
                }
                setListLoading();
                var q = 'city=' + encodeURIComponent(city) + '&district=' + encodeURIComponent(district);
                fetch(routes.aileHekimleri + '?' + q, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                    .then(function (x) {
                        if (!x.ok) {
                            setListMessage(x.j.message || 'Liste yüklenemedi.');
                            return;
                        }
                        renderDoctors(x.j.doctors || []);
                    })
                    .catch(function () {
                        setListMessage('Aile hekimi listesi yüklenemedi.');
                    });
            }

            chk.addEventListener('change', toggleFields);
            selCity.addEventListener('change', function () {
                oldDistrict = '';
                loadDistricts();
            });
            selDistrict.addEventListener('change', loadAileHekimleri);

            toggleFields();
            if (chk.checked && oldCity && selCity.value === oldCity) {
                loadDistricts();
            }
        })();
    </script>
    <script>
        (function () {
            var birthEl = document.getElementById('birth_date');
            var veliWrap = document.getElementById('veli_tc_wrap');
            var veliBadge = document.getElementById('veli_required_badge');
            var oncelikUyari = document.getElementById('oncelik_yas_uyari');
            var oncelikEsik = {{ (int) \App\Models\User::ONCELIKLI_YAS_ESIGI }};
            if (!birthEl || !veliWrap) return;

            function isUnder18(dStr) {
                if (!dStr) return false;
                var d = new Date(dStr + 'T12:00:00');
                if (isNaN(d.getTime())) return false;
                var limit = new Date();
                limit.setFullYear(limit.getFullYear() - 18);
                return d > limit;
            }

            function ageYears(dStr) {
                if (!dStr) return null;
                var d = new Date(dStr + 'T12:00:00');
                if (isNaN(d.getTime())) return null;
                var today = new Date();
                var y = today.getFullYear() - d.getFullYear();
                var m = today.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < d.getDate())) y--;
                return y;
            }

            function syncVeli() {
                var minor = isUnder18(birthEl.value);
                var veliInp = document.getElementById('veli_tc_kimlik_no');
                var hasVeli = veliInp && veliInp.value.replace(/\D/g, '').length > 0;
                if (minor || hasVeli) {
                    veliWrap.classList.remove('hidden');
                } else {
                    veliWrap.classList.add('hidden');
                }
                if (veliBadge) {
                    if (minor) veliBadge.classList.remove('hidden');
                    else veliBadge.classList.add('hidden');
                }
                if (oncelikUyari) {
                    var a = ageYears(birthEl.value);
                    if (a !== null && a >= oncelikEsik) oncelikUyari.classList.remove('hidden');
                    else oncelikUyari.classList.add('hidden');
                }
            }

            birthEl.addEventListener('change', syncVeli);
            birthEl.addEventListener('input', syncVeli);
            syncVeli();
        })();
    </script>
@endsection

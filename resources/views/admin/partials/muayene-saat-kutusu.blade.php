{{--
  İki satır muayene aralığı (öğleden önce / öğleden sonra). Kayıtta 5 güne çevrilir.
  @param string $idSuffix benzersiz DOM id son eki
  @param string $ogleOnceBaslangicName
  @param string $ogleOnceBitisName
  @param string $ogleSonraBaslangicName
  @param string $ogleSonraBitisName
  @param string $ogleOnceBaslangicValue
  @param string $ogleOnceBitisValue
  @param string $ogleSonraBaslangicValue
  @param string $ogleSonraBitisValue
  @param int $slotMinutes önizleme için varsayılan (showSlotDakika yoksa kullanılır)
  @param bool $showSlotDakika true ise randevu_slot_dakika alanı gösterilir ve önizleme buna bağlanır
  @param int|string|null $slotDakikaValue
  @param string|null $slotDakikaName form alan adı (varsayılan randevu_slot_dakika)
--}}
@php
    $idSuffix = $idSuffix ?? 'kurum';
    $slotMinutes = (int) ($slotMinutes ?? 30);
    $onceBasId = 'muayeneOnceBas'.$idSuffix;
    $onceBitId = 'muayeneOnceBit'.$idSuffix;
    $sonraBasId = 'muayeneSonraBas'.$idSuffix;
    $sonraBitId = 'muayeneSonraBit'.$idSuffix;
    $ozetId = 'muayeneOzet'.$idSuffix;
    $requiredAttr = ($required ?? true) ? 'required' : '';
    $requiredFlag = ($required ?? true) ? '1' : '0';
    $showSlotDakika = ! empty($showSlotDakika);
    $slotDakikaId = $showSlotDakika ? 'randevuSlotDakika'.$idSuffix : null;
    $slotDakikaName = $slotDakikaName ?? 'randevu_slot_dakika';
    $slotDakikaValue = (int) ($slotDakikaValue ?? 30);
@endphp
<div class="rounded-2xl border border-sky-200/90 bg-white/90 p-4 space-y-3"
     data-mhrs-muayene-wrap="1"
     data-mhrs-once-bas="{{ $onceBasId }}"
     data-mhrs-once-bit="{{ $onceBitId }}"
     data-mhrs-sonra-bas="{{ $sonraBasId }}"
     data-mhrs-sonra-bit="{{ $sonraBitId }}"
     data-mhrs-muayene-ozet="{{ $ozetId }}"
     data-mhrs-required="{{ $requiredFlag }}"
     data-mhrs-slot-min="{{ $slotMinutes }}"
     @if ($slotDakikaId) data-mhrs-slot-input-id="{{ $slotDakikaId }}" @endif>
    @if (! empty($label))
        <p class="text-xs font-bold text-slate-800">{{ $label }}</p>
    @endif
    <p class="text-[11px] text-slate-600 leading-relaxed">
        @if ($showSlotDakika)
            {{ $hint ?? 'Hafta içi (Pazartesi–Cuma) her gün aynı saatler uygulanır. Randevu dilimini (dakika) aşağıdan ayarlayın; gün içi slot sayısı otomatik hesaplanır.' }}
        @else
            {{ $hint ?? 'Hafta içi (Pazartesi–Cuma) her gün aynı saatler uygulanır. Randevu dilimi '.$slotMinutes.' dakikadır; gün içi slot sayısı otomatik hesaplanır.' }}
        @endif
    </p>
    <div class="grid gap-3 md:grid-cols-2">
        <div class="rounded-xl border border-sky-100 bg-sky-50/40 p-3">
            <p class="text-xs font-bold text-slate-700">Öğleden önce</p>
            <div class="mt-2 flex flex-wrap items-end gap-4">
                <div class="min-w-[8rem]">
                    <label for="{{ $onceBasId }}" class="text-xs font-bold text-slate-700">Başlangıç</label>
                    <input type="time" name="{{ $ogleOnceBaslangicName }}" id="{{ $onceBasId }}" value="{{ $ogleOnceBaslangicValue }}" step="300" {{ $requiredAttr }}
                           class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div class="min-w-[8rem]">
                    <label for="{{ $onceBitId }}" class="text-xs font-bold text-slate-700">Bitiş</label>
                    <input type="time" name="{{ $ogleOnceBitisName }}" id="{{ $onceBitId }}" value="{{ $ogleOnceBitisValue }}" step="300" {{ $requiredAttr }}
                           class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-sky-100 bg-sky-50/40 p-3">
            <p class="text-xs font-bold text-slate-700">Öğleden sonra</p>
            <div class="mt-2 flex flex-wrap items-end gap-4">
                <div class="min-w-[8rem]">
                    <label for="{{ $sonraBasId }}" class="text-xs font-bold text-slate-700">Başlangıç</label>
                    <input type="time" name="{{ $ogleSonraBaslangicName }}" id="{{ $sonraBasId }}" value="{{ $ogleSonraBaslangicValue }}" step="300" {{ $requiredAttr }}
                           class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div class="min-w-[8rem]">
                    <label for="{{ $sonraBitId }}" class="text-xs font-bold text-slate-700">Bitiş</label>
                    <input type="time" name="{{ $ogleSonraBitisName }}" id="{{ $sonraBitId }}" value="{{ $ogleSonraBitisValue }}" step="300" {{ $requiredAttr }}
                           class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap items-end gap-4">
        @if ($showSlotDakika && $slotDakikaId)
            <div class="min-w-[10rem]">
                <label for="{{ $slotDakikaId }}" class="text-xs font-bold text-slate-700">Randevu dilimi (dk)</label>
                <input type="number" name="{{ $slotDakikaName }}" id="{{ $slotDakikaId }}" value="{{ $slotDakikaValue }}" min="5" max="120" step="1" @if ($required ?? true) required @endif
                       class="mt-1 w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                <p class="mt-1 text-[10px] text-slate-500">5–120 dakika</p>
            </div>
        @endif
    </div>
    <p id="{{ $ozetId }}" class="text-xs font-semibold text-emerald-900 bg-emerald-50/80 rounded-xl border border-emerald-100 px-3 py-2"></p>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                function parseMins(t) {
                    if (!t || typeof t !== 'string') return null;
                    var p = t.split(':');
                    if (p.length < 2) return null;
                    var h = parseInt(p[0], 10);
                    var m = parseInt(p[1], 10);
                    if (isNaN(h) || isNaN(m)) return null;
                    return h * 60 + m;
                }
                function clampSlotMin(n) {
                    if (isNaN(n) || n < 5) return 5;
                    if (n > 120) return 120;
                    return n;
                }
                function slotMinFromWrap(wrap) {
                    var sid = wrap.getAttribute('data-mhrs-slot-input-id');
                    if (sid) {
                        var si = document.getElementById(sid);
                        if (si) return clampSlotMin(parseInt(si.value, 10));
                    }
                    return clampSlotMin(parseInt(wrap.getAttribute('data-mhrs-slot-min') || '30', 10));
                }
                function calculateSlots(startMins, endMins, slotMin) {
                    if (startMins === null || endMins === null || endMins <= startMins) return null;
                    return Math.floor((endMins - startMins) / slotMin);
                }
                function updateOzetFromIds(onceBasId, onceBitId, sonraBasId, sonraBitId, outId, slotMin, required) {
                    var ob = document.getElementById(onceBasId);
                    var oe = document.getElementById(onceBitId);
                    var sb = document.getElementById(sonraBasId);
                    var se = document.getElementById(sonraBitId);
                    var o = document.getElementById(outId);
                    if (!ob || !oe || !sb || !se || !o) return;
                    var obm = parseMins(ob.value);
                    var oem = parseMins(oe.value);
                    var sbm = parseMins(sb.value);
                    var sem = parseMins(se.value);
                    if (obm === null || oem === null || sbm === null || sem === null) {
                        if (!required && !ob.value && !oe.value && !sb.value && !se.value) {
                            o.textContent = 'Boş: bu birim için şimdilik program tanımlanmaz (doktoru olan birimlerde saat zorunludur).';
                        } else {
                            o.textContent = required ? 'Öğleden önce ve öğleden sonra başlangıç/bitiş saatlerini girin.' : 'Tüm saat alanlarını doldurun veya hepsini boş bırakın.';
                        }
                        return;
                    }
                    var onceSlots = calculateSlots(obm, oem, slotMin);
                    var sonraSlots = calculateSlots(sbm, sem, slotMin);
                    if (onceSlots === null || sonraSlots === null) {
                        o.textContent = 'Bitiş saatleri başlangıçtan sonra olmalıdır.';
                        return;
                    }
                    var total = onceSlots + sonraSlots;
                    o.textContent = 'Gün başına yaklaşık ' + total + ' randevu slotu (' + slotMin + ' dk). Hafta içi 5 gün için toplam yaklaşık ' + (total * 5) + ' slot.';
                }
                function bindWrap(wrap) {
                    var onceBasId = wrap.getAttribute('data-mhrs-once-bas');
                    var onceBitId = wrap.getAttribute('data-mhrs-once-bit');
                    var sonraBasId = wrap.getAttribute('data-mhrs-sonra-bas');
                    var sonraBitId = wrap.getAttribute('data-mhrs-sonra-bit');
                    var outId = wrap.getAttribute('data-mhrs-muayene-ozet');
                    var required = wrap.getAttribute('data-mhrs-required') !== '0';
                    var slotInputId = wrap.getAttribute('data-mhrs-slot-input-id');
                    if (!onceBasId || !onceBitId || !sonraBasId || !sonraBitId || !outId) return;
                    var run = function () {
                        updateOzetFromIds(onceBasId, onceBitId, sonraBasId, sonraBitId, outId, slotMinFromWrap(wrap), required);
                    };
                    var onceBasEl = document.getElementById(onceBasId);
                    var onceBitEl = document.getElementById(onceBitId);
                    var sonraBasEl = document.getElementById(sonraBasId);
                    var sonraBitEl = document.getElementById(sonraBitId);
                    [onceBasEl, onceBitEl, sonraBasEl, sonraBitEl].forEach(function (el) {
                        if (!el) return;
                        el.addEventListener('change', run);
                        el.addEventListener('input', run);
                    });
                    if (slotInputId) {
                        var si = document.getElementById(slotInputId);
                        if (si) {
                            si.addEventListener('change', run);
                            si.addEventListener('input', run);
                        }
                    }
                    run();
                }
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('[data-mhrs-muayene-wrap]').forEach(bindWrap);
                });
            })();
        </script>
    @endpush
@endonce

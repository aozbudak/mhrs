<?php

namespace App\Enums;

enum GunlukDegisimTur: string
{
    /** O gün şablondan bağımsız olarak kapalı (izin, tatil vb.). */
    case Kapali = 'kapali';

    /** O gün için haftalık şablon yerine gunluk_degisim_saatleri satırları kullanılır. */
    case OzelSaatler = 'ozel_saatler';
}

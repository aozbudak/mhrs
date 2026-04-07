<?php

namespace App\Enums;

enum RandevuDurumu: string
{
    case Bekliyor = 'bekliyor';

    /** Sistem tarafından anında onaylanmış randevu (7/24). */
    case Onaylandi = 'onaylandi';

    case Tamamlandi = 'tamamlandi';
    case Iptal = 'iptal';
    case Gelmedi = 'gelmedi';
}

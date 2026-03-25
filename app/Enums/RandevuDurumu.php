<?php

namespace App\Enums;

enum RandevuDurumu: string
{
    case Bekliyor = 'bekliyor';
    case Tamamlandi = 'tamamlandi';
    case Iptal = 'iptal';
    case Gelmedi = 'gelmedi';
}

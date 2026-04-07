<?php

namespace App\Enums;

enum RandevuKatilimDurumu: string
{
    case YanitBekleniyor = 'yanit_bekleniyor';
    case Gelecek = 'gelecek';
    case Gelemeyecek = 'gelemeyecek';
}

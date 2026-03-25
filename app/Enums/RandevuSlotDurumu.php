<?php

namespace App\Enums;

enum RandevuSlotDurumu: string
{
    case Musait = 'musait';
    case Rezerve = 'rezerve';
    case Iptal = 'iptal';
    case Bloklandi = 'bloklandi';
}

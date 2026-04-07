<?php

namespace App\Enums;

enum RandevuSlotTipi: string
{
    /** Her hasta tarafından seçilebilir. */
    case Normal = 'normal';

    /** Yalnızca öncelikli hasta (65 yaş üstü veya engelli) tarafından seçilebilir. */
    case Oncelikli = 'oncelikli';
}

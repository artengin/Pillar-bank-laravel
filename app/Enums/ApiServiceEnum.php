<?php

namespace App\Enums;

use RonasIT\Support\Traits\EnumTrait;

enum ApiServiceEnum: string
{
    use EnumTrait;

    case KYC = 'KYC';
}

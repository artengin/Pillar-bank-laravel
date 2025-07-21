<?php

namespace App\Enums\Card;

use RonasIT\Support\Traits\EnumTrait;

enum StatusEnum: string
{
    use EnumTrait;

    case Active = 'active';
    case Freeze = 'freeze';
    case Lost = 'lost';
    case Broken = 'broken';
    case Reissued = 'reissued';
}

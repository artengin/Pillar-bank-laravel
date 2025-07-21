<?php

namespace App\Enums\Transaction;

use RonasIT\Support\Traits\EnumTrait;

enum TypeEnum: string
{
    use EnumTrait;

    case Incoming = 'incoming';
    case Outgoing = 'outgoing';
}

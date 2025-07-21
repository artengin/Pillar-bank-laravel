<?php

namespace App\Enums\User;

use RonasIT\Support\Traits\EnumTrait;

enum StatusEnum: string
{
    use EnumTrait;

    case Approve = 'approve';
    case Reject = 'reject';
}

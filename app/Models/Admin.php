<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use RonasIT\Support\Traits\ModelTrait;

/**
 * @property string email
 * @property string password
 */
class Admin extends Authenticatable
{
    use ModelTrait;
}

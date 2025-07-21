<?php

namespace App\Models;

use App\Enums\User\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use RonasIT\Support\Traits\ModelTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string first_name
 * @property string last_name
 * @property string phone
 * @property string email
 * @property string ssn
 * @property string password
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property StatusEnum status
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use ModelTrait;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'ssn',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
      'status' => StatusEnum::class,
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}

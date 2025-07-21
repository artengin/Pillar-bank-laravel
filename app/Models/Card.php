<?php

namespace App\Models;

use App\Enums\Card\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use RonasIT\Support\Traits\ModelTrait;

/**
 * @property integer id
 * @property string name
 * @property integer number
 * @property integer user_id
 * @property integer balance
 * @property Carbon finished_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property StatusEnum status
 * @property int reissued_id
 */
class Card extends Model
{
    use ModelTrait;
    use Notifiable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'number',
        'balance',
        'finished_at',
        'status',
        'reissued_id',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    protected $hidden = ['pivot'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

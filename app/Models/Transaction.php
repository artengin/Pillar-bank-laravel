<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RonasIT\Support\Traits\ModelTrait;

/**
 * @property integer card_id
 * @property string name
 * @property integer card_number
 * @property integer amount
 */
class Transaction extends Model
{
    use ModelTrait;

    protected $fillable = [
        'card_id',
        'name',
        'card_number',
        'amount',
        'type'
    ];

    protected $hidden = ['pivot'];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatingUpgradeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_cid',
        'token_id',
        'rating',
        'completed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cid', 'cid');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    use HasFactory;

    protected $primaryKey = 'token_instance_id'; // Use token_instance_id as the primary key
    public $incrementing = true; // Auto-incrementing primary key
    protected $keyType = 'int'; // Primary key is an integer

    protected $fillable = [
        'id',
        'user_cid',
        'exam_id',
        'issued_at',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cid', 'cid');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}

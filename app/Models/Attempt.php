<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attempt extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $timestamps=false;

    protected $fillable = [
        'id',
        'user_cid',
        'exam_id',
        'attempt_number',
        'attempted_at',
        'status',
        'score',
        'cooldown_until',
        'question_ids',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'cooldown_until' => 'datetime',
        'question_ids' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cid', 'cid');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'attempt_id');
    }

    public function getPercentageAttribute()
    {
        $questions = $this->exam->questions->whereIn('id', $this->question_ids ?? []);
        $maxScore = $questions->sum('points');
        if ($maxScore == 0) return 0;
        return round(($this->score / $maxScore) * 100, 1);
    }
}

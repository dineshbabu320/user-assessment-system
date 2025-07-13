<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    public $timestamps=false;
    protected $fillable = ['name','question_limit','subdivision_question_limit','category_ratios'];

    protected $casts = [
        'category_ratios' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'exam_id');
    }
}

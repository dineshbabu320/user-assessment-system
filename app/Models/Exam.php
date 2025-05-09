<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    public $timestamps=false;
    protected $fillable = ['name','question_limit'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'exam_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{

    protected $fillable = ['user_id', 'exam_id', 'token_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}

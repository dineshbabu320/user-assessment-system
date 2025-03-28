<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'cid';

    protected $fillable = ['cid', 'name', 'email','atc_rating', 'role_id', 'subdivision_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}
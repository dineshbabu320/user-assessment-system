<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'cid'; // VATSIM ID is the primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cid',
        'name',
        'email',
        'atc_rating',
        'role_id',
        'subdivision_id',
    ];

    public function tokens() {
        return $this->hasMany(Token::class, 'user_cid');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'user_cid');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'user_cid', 'cid');
    }

    // Role Helper Methods
    public function isAdmin(): bool
    {
        return $this->role_id == 3;
    }

    public function isSubdivisionDirector(): bool
    {
        return $this->role_id == 2;
    }

    public function roleAboveStudent(): bool
    {
        return $this->role_id > 1;
    }
}

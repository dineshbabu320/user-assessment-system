<?php
namespace App\Models;

use App\Http\Controllers\OAuthController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use League\OAuth2\Client\Token\AccessToken;

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
        'subdivision_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function tokens()
    {
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

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_cid', 'role_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'user_cid', 'cid');
    }

    // Role Helper Methods
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('Student');
    }

    public function isSubdivisionStaff(): bool
    {
        return $this->hasRole('Subdivision Staff');
    }

    public function isMentor()
    {
        return $this->hasRole("Mentor");
    }

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function isExaminer()
    {
        return $this->hasRole('Examiner');
    }
    public function subdivisionNotifications()
    {
        return $this->hasMany(AdminSubdivisionNotification::class, 'admin_cid', 'cid');
    }

    public function getTokenAttribute()
    {
        if ($this->access_token === null)
            return null;
        else {
            $token = new AccessToken([
                'access_token' => $this->access_token,
                'refresh_token' => $this->refresh_token,
                'expires' => $this->token_expires_at ? $this->token_expires_at->timestamp : null,
            ]);

            if ($token->hasExpired()) {
                $token = OAuthController::updateToken($token);
            }

            // Can't put it inside the "if token expired"; $this is null there
            // but anyway Laravel will only update if any changes have been made.
            $this->update([
                'access_token' => ($token) ? $token->getToken() : null,
                'refresh_token' => ($token) ? $token->getRefreshToken() : null,
                'token_expires_at' => ($token && $token->getExpires()) ? \Carbon\Carbon::createFromTimestamp($token->getExpires()) : null,
            ]);

            return $token;
        }
    }

    public function canSearchAllStudents()
    {
        return $this->isAdmin();
    }

    public function canViewStudent($student)
    {
        return $this->isAdmin() || $this->subdivision_id === $student->subdivision_id;
    }
}

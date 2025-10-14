<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type_id',
        'name',
        'email',
        'password',
        'token',
        'cpf',
        'city',
        'state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    /**
     * @return HasOne
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function manager()
    {
        return $this->hasOne(Manager::class, 'user_id');
    }

    /**
     * Create a passport access token for user
     */
    public function createAccessToken()
    {
        Log::error("DEBUG -> \t\tROLE: ". $this->type->role);
        Log::error("DEBUG -> \t\tROLE: ". Passport::scopes());
        Log::error("DEBUG -> \t\tROLE: ". Passport::hasScope($this->type->role));
        $this->newAccessToken = $this->createToken($this->id.'_token', [$this->type->role])->accessToken;
        //dd($this->newAccessToken);
        return $this->newAccessToken;
    }
}

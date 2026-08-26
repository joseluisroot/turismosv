<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'is_profile_public' => false,
        'public_name_mode' => 'alias',
        'show_public_achievements' => true,
        'show_public_stamps' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'terms_accepted_at',
        'terms_version',
        'points_balance',
        'public_profile_id','is_profile_public','public_name_mode','public_alias','show_public_achievements','show_public_stamps','public_profile_updated_at',
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
            'terms_accepted_at' => 'datetime',
            'points_balance' => 'integer',
            'is_profile_public' => 'boolean','show_public_achievements' => 'boolean','show_public_stamps' => 'boolean','public_profile_updated_at' => 'datetime',
        ];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function passportStamps(): HasMany
    {
        return $this->hasMany(PassportStamp::class);
    }

    public function achievements(): BelongsToMany { return $this->belongsToMany(Achievement::class,'user_achievements')->withPivot(['passport_stamp_id','earned_at'])->withTimestamps(); }
    public function pointTransactions(): HasMany { return $this->hasMany(PointTransaction::class); }
}

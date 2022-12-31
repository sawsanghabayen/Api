<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    protected $appends = ['ads_count'];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'verified',
        // 'created_at',
        'updated_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function getAdsCountAttribute()
    {
        return $this->ads()->count();
    }

    public function ads()
    {
        return $this->hasMany(Ad::class, 'user_id', 'id');
    }

    public function favoriteAds()
    {
        return $this->belongsToMany(Ad::class, FavoriteAd::class, 'user_id', 'ad_id');
    }

    public function reviewAds()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function messages()
{
    return $this->hasMany(Message::class, 'user_id', 'id');
}
    public function chats()
{
    return $this->hasMany(Chat::class, 'sender_id', 'id');
}

public function blockedUsers()
{
    return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_user_id');
}

}

<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsTo
};
use App\Traits\UuidTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UuidTrait, ImageTrait;

    public $guarded = ['created_at'];

    protected $modelName = 'user';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userDetail (): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    public function setPasswordAttribute ($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function role (): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function addresses (): HasMany
    {
        return $this->hasMany(UserAddress::class,'user_id', 'id');
    }
}

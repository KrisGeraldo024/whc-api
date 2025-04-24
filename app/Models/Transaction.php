<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    UuidTrait,
};
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes
};

class Transaction extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'transaction';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function items (): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function shipping (): HasOne
    {
        return $this->hasOne(TransactionShipping::class);
    }

    public function user (): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userAddress (): HasOne
    {
        return $this->hasOne(UserAddress::class, 'id', 'user_address_id');
    }

    public function userDetail (): HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'user_id');
    }
}

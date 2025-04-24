<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    UuidTrait
};
use Illuminate\Database\Eloquent\Relations\{
    HasMany, 
    HasOne
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes
};

class UserAddress extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'user_address';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function user (): HasOne
    {
        return $this->hasOne(User::class,'id', 'user_id');
    }
}

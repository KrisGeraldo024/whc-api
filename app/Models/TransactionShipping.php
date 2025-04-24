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

class TransactionShipping extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'transaction_shipping';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function logs (): hasMany
    {
        return $this->hasMany(TransactionShippingLog::class,'transaction_shipping_id', 'id');
    }
}

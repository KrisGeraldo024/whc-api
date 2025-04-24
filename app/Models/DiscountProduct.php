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

class DiscountProduct extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'discount_product';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function discount (): HasOne
    {
        return $this->hasOne(Discount::class,'id', 'discount_id');
    }

    public function accessory (): HasOne
    {
        return $this->hasOne(Accessories::class,'id', 'product_id');
    }
}

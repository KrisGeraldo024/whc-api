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

class TransactionItem extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'transaction_item';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function accessory (): hasOne
    {
        return $this->hasOne(Accessories::class,'id', 'product_id');
    }

    public function variation (): hasOne
    {
        return $this->hasOne(Variation::class,'id', 'product_variation_id');
    }
    
    public function discount (): HasOne
    {
        return $this->hasOne(Discount::class, 'id', 'discount_id');
    }

}

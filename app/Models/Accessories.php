<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait
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

class Accessories extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'accessories';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function discount (): HasOne
    {
        return $this->hasOne(DiscountProduct::class,'product_id', 'id')
        ->where('expired', 0);
    }

    public function variations (): HasMany
    {
        return $this->hasMany(Variation::class,'product_id', 'id');
    }

    public function itemFeatures (): HasMany
    {
        return $this->hasMany(AccessoriesItem::class)->whereType('feature');
    }

    public function itemSpecifications (): HasMany
    {
        return $this->hasMany(AccessoriesItem::class)->whereType('specification');
    }
}

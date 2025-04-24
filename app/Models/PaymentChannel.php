<?php

namespace App\Models;
use App\Traits\{
    GlobalTrait, 
    FileTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait,
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

class PaymentChannel extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'payment_channel';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
}

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

class PaymentOption extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'payment_option';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

    public function payment_methods(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function payment_channels(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class,'payment_channel_id');
    }

    public function payment_platforms(): BelongsTo
    {
        return $this->belongsTo(PaymentPlatform::class,'payment_platform_id');
    }
}

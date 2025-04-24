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
class PaymentPlatform extends Model 
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'payment_platform';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $hidden = [ 'deleted_at'];

    // Add this relationship
    public function paymentMethod(): BelongsTo 
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function logs () : HasMany
    {
        return $this->hasMany(Log::class, 'item_id', 'id');
    }
    
    public function accordions () : HasMany
    {
        return $this->hasMany(Accordion::class, 'parent', 'id');
    }
}
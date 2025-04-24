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

class Amenity extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $modelName = 'amenity';

    protected $hidden = [
        'deleted_at'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }


}

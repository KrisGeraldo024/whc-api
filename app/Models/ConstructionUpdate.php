<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    ImageTrait, 
    UuidTrait,
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

class ConstructionUpdate extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $modelName = 'construction_update';

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


}
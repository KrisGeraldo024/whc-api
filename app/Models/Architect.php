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

class Architect extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $modelName = 'architect';

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


}

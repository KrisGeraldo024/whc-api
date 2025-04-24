<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    ImageTrait, 
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

class Floorplan extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $modelName = 'floorplan';

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


}

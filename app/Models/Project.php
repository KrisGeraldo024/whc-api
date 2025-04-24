<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    ImageTrait, 
    UuidTrait,
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

class Project extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'project';

    protected $table = 'projects';

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function project_status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function vicinity(): HasMany
    {
    return $this->hasMany(Vicinity::class);
    }

    public function amenity(): HasMany
    {
    return $this->hasMany(Amenity::class);
    }

    public function floorplan(): HasMany
    {
    return $this->hasMany(Floorplan::class);
    }

    public function architect(): HasMany
    {
    return $this->hasMany(Architect::class);
    }

    public function construction_update(): HasMany
    {
    return $this->hasMany(ConstructionUpdate::class);
    }

    public function project_award(): HasMany
    {
    return $this->hasMany(ProjectAward::class);
    }
    


    /*
    public function vicinity(): BelongsTo
    {
        return $this->belongsTo(Vicinity::class);
    }

    public function floorplan(): HasMany
    {
        return $this->hasMany(Floorplan::class);
    }

    public function amenity(): HasMany
    {
        return $this->hasMany(Amenity::class);
    }

    public function architect(): HasMany
    {
        return $this->hasMany(ArchitectPerspective::class);
    }

    public function construction(): HasMany
    {
        return $this->hasMany(ConstructionUpdate::class);
    }*/
}
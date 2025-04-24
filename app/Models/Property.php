<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait
};
use PhpParser\Node\Expr\FuncCall;

class Property extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'property';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'deleted_at'
    ];

    
    protected $appends = ['gmaps_embed_link'];

    public function locations(): HasOne
    {
        return $this->hasOne(Taxonomy::class, 'id', 'location_id')
                    ->where('type', Taxonomy::TYPE_PROPERTY_LOCATION);
    }

    // Define property type relationship
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'property_type_id', 'id')
                    ->where('type', Taxonomy::TYPE_UNIT);
    }

    public function propertyStatus(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'status_id', 'id')
                    ->where('type', Taxonomy::TYPE_PROPERTY_STATUS);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'parent_id', 'id');
    }

    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'parent_id', 'id');
    }

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class, 'page_id', 'id');
    }

    public function landmarks(): HasMany
    {
        return $this->hasMany(Landmark::class, 'parent_id', 'id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id', 'id');
    }

    public function videos(): HasOne
    {
        return $this->hasOne(Video::class, 'parent_id', 'id');
    }

    public function getGmapsEmbedLinkAttribute(): ?string 
    {
        return $this->gmaps_link ? $this->generateGmapsEmbedCode($this->gmaps_link) : null;
    }
    
}
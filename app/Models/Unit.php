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

class Unit extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'unit';

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
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parent_id', 'id');
    }
    // Define property type relationship
    public function unitType(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'unit_type', 'id')
                    ->where('type', Taxonomy::TYPE_UNIT);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'parent_id', 'id');
    }

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class, 'page_id', 'id');
    }

    public function videos(): HasOne
    {
        return $this->hasOne(Video::class, 'parent_id', 'id');
    }

    
    public function getGmapsEmbedLinkAttribute(): ?string
    {
        return $this->gmap_url ? $this->generateGmapsEmbedCode($this->gmap_url) : null;
    }
    
}
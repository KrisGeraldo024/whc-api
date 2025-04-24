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

class PageTemplate extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'page_templates';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'services_data',
        'sample_date'
    ];

    public function taxServices(): HasMany
    {
        return $this->hasMany(taxServices::class,'services', 'id');
    }

    public function getServicesDataAttribute()
    {
        return $this->services === 1 ? taxServices::with(['taxServicesDetails'])->get() : 'No';
    }

    public function getSampleDateAttribute()
    {
        return $this->sequence === 1 ? 'Yes' : 'No';
    }


}

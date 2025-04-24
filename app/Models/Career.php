<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};
use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait
};

class Career extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'career';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function location(): HasOne
    {
        return $this->hasOne(Taxonomy::class, 'id', 'location_id')
                    ->where('type', Taxonomy::TYPE_JOB_LOCATION);
    }

    public function employment_type(): HasOne
    {
        return $this->hasOne(Taxonomy::class, 'id', 'employment_type_id')
                    ->where('type', Taxonomy::TYPE_EMPLOYMENT);
    }


}

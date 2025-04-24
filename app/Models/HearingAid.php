<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    HasOne
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait
};

class HearingAid extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'hearing_aid';

    public function hearingAidCategory (): BelongsTo
    {
        return $this->belongsTo(HearingAidCategory::class);
    }

    public function items (): HasMany
    {
        return $this->hasMany(HearingAidItem::class);
    }

    public function itemFeatures (): HasMany
    {
        return $this->hasMany(HearingAidItem::class)->whereType('feature');
    }

    public function itemSpecifications (): HasMany
    {
        return $this->hasMany(HearingAidItem::class)->whereType('specification');
    }

    public function itemUvp (): HasMany
    {
        return $this->hasMany(HearingAidItem::class)->whereType('uvp');
    }

    public function formDetails (): HasOne
    {
        return $this->hasOne(FormDetails::class, 'parent_id', 'id');
    }
}
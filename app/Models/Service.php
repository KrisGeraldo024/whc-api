<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait
};

class Service extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'service';

    public function serviceCategory (): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function itemServices (): HasMany
    {
        return $this->hasMany(ServiceItem::class)->whereType('service');
    }

    public function itemFaqs (): HasMany
    {
        return $this->hasMany(ServiceItem::class)->whereType('faq');
    }

    public function itemProcesses (): HasMany
    {
        return $this->hasMany(ServiceItem::class)->whereType('process');
    }
}
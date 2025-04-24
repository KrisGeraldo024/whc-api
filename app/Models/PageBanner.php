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
class PageBanner extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'page_banner';

    public function uvp (): BelongsTo
    {
        return $this->belongsTo(Pages::class);
    }
}

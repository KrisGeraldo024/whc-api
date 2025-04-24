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
    ImageTrait,
    UuidTrait
};

class PageUvp extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'page_uvp';

    public function uvp (): BelongsTo
    {
        return $this->belongsTo(Pages::class);
    }

    public function uvpDetails(): HasMany
    {
        return $this->hasMany(UvpDetails::class,'uvp_id', 'id');
    }
}

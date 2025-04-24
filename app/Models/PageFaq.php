<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
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

class PageFaq extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $modelName = 'page_faq';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];
}
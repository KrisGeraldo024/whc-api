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

class PageTag extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $modelName = 'page_tag';

    protected $table = 'page_tags';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];
}
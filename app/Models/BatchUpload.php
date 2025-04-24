<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait,
    ImageTrait,
    UuidTrait,
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes
};
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    HasOne
};

class BatchUpload extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $modelName = 'batch_upload';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];
}
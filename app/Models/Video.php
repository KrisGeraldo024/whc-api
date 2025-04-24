<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo
};
use App\Traits\{
    ImageTrait,
    UuidTrait
};

class Video extends Model
{
    use HasFactory, SoftDeletes,ImageTrait, UuidTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'video';

    public function videoCategory (): BelongsTo
    {
        return $this->belongsTo(VideoCategory::class);
    }
}
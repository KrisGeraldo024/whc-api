<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    FileTrait, 
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

class PageFile extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, FileTrait, SoftDeletes, ImageTrait;

    protected $modelName = 'page_file';

    public $timestamps = true;

    protected $appends = ['tag_title'];

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'date:F, j Y',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(PageTag::class, 'tag_id');
    }

    public function getTagTitleAttribute()
    {
    return $this->tag->title;;
    }

    public function File(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id','parent_id');
    }
    
}
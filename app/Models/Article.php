<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait,
    GlobalTrait
};

class Article extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait, GlobalTrait;

    protected $modelName = 'article';
    public $timestamps = true;

    protected $guarded = [  
        'id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function articleCategory (): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'category_id', 'id')
        ->where('type', Taxonomy::TYPE_ARTICLE_CATEGORY);
    }
}
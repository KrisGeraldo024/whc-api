<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    GlobalTrait,
    UuidTrait
};
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};

use App\Services\Page\PageBannerService;

class Page extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, GlobalTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'page';

    public function banners(): HasMany
    {
        return $this->hasMany(PageBanner::class,'page_id', 'id');
    }

    public function ctas(): HasMany
    {
        return $this->hasMany(PageCta::class,'page_id', 'id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(PageCard::class,'page_id', 'id');
    }

    public function uvps(): HasMany
    {
        return $this->hasMany(PageUvp::class,'page_id', 'id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(PageFaq::class,'page_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PageFile::class,'page_id', 'id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(PageTag::class,'page_id', 'id');
    }

    public function page_sections() : HasMany
    {
        return $this->hasMany(PageSection::class, 'page_id', 'id');
    }
}
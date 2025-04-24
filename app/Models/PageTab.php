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

class PageTab extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $modelName = 'page_tab';

    protected $table = 'page_tabs';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function tabCard(): HasMany
    {
        return $this->hasMany(PageCard::class,'tab_id', 'id');
    }

    public function tabUvp(): HasMany
    {
        return $this->hasMany(PageUvp::class,'tab_id', 'id');
    }

    public function tabFile(): HasMany
    {
        return $this->hasMany(PageFile::class,'tab_id', 'id');
    }

}

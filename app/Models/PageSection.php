<?php

namespace App\Models;

use App\Traits\FileTrait;
use App\Traits\GlobalTrait;
use App\Traits\ImageTrait;
use App\Traits\MetadataTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageSection extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, GlobalTrait, MetadataTrait, FileTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'page_section';

    public function buttons () : HasMany
    {
        return $this->hasMany(Button::class, 'parent', 'id');
    }

    public function logs () : HasMany
    {
        return $this->hasMany(Log::class, 'item_id', 'id');
    }

    public function accordions () : HasMany
    {
        return $this->hasMany(Accordion::class, 'parent', 'id');
    }
}
        
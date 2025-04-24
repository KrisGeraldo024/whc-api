<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    HasMany
};
use App\Traits\UuidTrait;

class ArticleCategory extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function articles (): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
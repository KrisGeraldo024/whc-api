<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    HasMany
};
use App\Traits\UuidTrait;

class Role extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'created_at'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function users (): HasMany
    {
        return $this->hasMany(User::class);
    }
}
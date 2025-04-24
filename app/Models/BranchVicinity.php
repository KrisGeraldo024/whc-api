<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};
use App\Traits\UuidTrait;

class BranchVicinity extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function branchRegion (): BelongsTo
    {
        return $this->belongsTo(BranchRegion::class);
    }

    public function branches (): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
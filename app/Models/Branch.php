<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
    HasOne
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait
};

class Branch extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'branch';

    public function branchRegion (): BelongsTo
    {
        return $this->belongsTo(BranchRegion::class);
    }

    public function branchVicinity (): BelongsTo
    {
        return $this->belongsTo(BranchVicinity::class);
    }

    public function promos (): HasMany
    {
        return $this->hasMany(Promo::class, 'branch_id', 'id');
    }

    public function formDetails (): HasOne
    {
        return $this->hasOne(FormDetails::class, 'parent_id', 'id');
    }
}

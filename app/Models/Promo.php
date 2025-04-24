<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasOne
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait
};

class Promo extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'promo';

    public function branch (): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function formDetails (): HasOne
    {
        return $this->hasOne(FormDetails::class, 'parent_id', 'id');
    }
}
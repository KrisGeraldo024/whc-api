<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo
};
use App\Traits\{
    ImageTrait,
    UuidTrait
};

class HearingAidItem extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'hearing_aid_item';

    public function hearingAid (): BelongsTo
    {
        return $this->belongsTo(HearingAid::class);
    }
}
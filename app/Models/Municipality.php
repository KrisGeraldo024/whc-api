<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    UuidTrait
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

class Municipality extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, SoftDeletes;

    protected $modelName = 'municipality';

    public $timestamps = true;

    protected $guarded = [
        'created_at'
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function province (): HasOne
    {
        return $this->hasOne(Province::class,'id', 'province_id');
    }

    public function barangays (): HasMany
    {
        return $this->hasMany(Barangay::class,'municipality_id', 'id');
    }
}

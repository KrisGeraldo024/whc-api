<?php

namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    FileTrait, 
    UuidTrait,
    ImageTrait,
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

class Biller extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, SoftDeletes;

    protected $modelName = 'biller';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

    public function projects(): BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}

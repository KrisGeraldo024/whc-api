<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    UuidTrait
};

use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};

class ProjectStatus extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $modelName = 'project_status';

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    
}

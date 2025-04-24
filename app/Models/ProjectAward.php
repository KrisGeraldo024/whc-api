<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};
use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait
};

class ProjectAward extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'project_award';

    protected $table = 'project_awards';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasOne,
};
use App\Traits\{
    MetadataTrait,
    ImageTrait,
    UuidTrait
};

class Appointment extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'appointment';

    public function branch (): HasOne
    {
        return $this->hasOne(Branch::class,'id', 'branch_id');
    }

    public function service (): HasOne
    {
        return $this->hasOne(Service::class,'id', 'service_id');
    }
}

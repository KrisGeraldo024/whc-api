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

class ServiceItem extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'service_item';

    public function service (): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
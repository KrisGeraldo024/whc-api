<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo
};
use App\Traits\UuidTrait;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    public $guarded = ['created_at'];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UuidTrait;

class Office extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    ImageTrait,
    UuidTrait
};

class History extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
    
    protected $modelName = 'history';
}

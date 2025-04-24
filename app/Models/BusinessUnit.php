<?php

namespace App\Models;

use App\Traits\GlobalTrait;
use App\Traits\ImageTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnit extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, GlobalTrait, ImageTrait;

    protected $modelName = 'business_unit';
    
    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

}

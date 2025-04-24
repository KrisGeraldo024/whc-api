<?php

namespace App\Models;

use App\Traits\GlobalTrait;
use App\Traits\ImageTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnitsDirectory extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, GlobalTrait, ImageTrait;

    protected $modelName = 'business_units_directory';
    
    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
}

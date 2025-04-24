<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    UuidTrait
};

class Inquiry extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
    
}

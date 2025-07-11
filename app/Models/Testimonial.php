<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    MetadataTrait
};

class Testimonial extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'testimonial';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
    
}
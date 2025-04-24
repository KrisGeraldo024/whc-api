<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\{
    GlobalTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait
};

class Story extends Model
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'story';

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];
}

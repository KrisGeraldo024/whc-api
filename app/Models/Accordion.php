<?php

namespace App\Models;


use App\Traits\GlobalTrait;
use App\Traits\ImageTrait;
use App\Traits\MetadataTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accordion extends Model
{
    use HasFactory,  SoftDeletes, UuidTrait, ImageTrait, GlobalTrait, MetadataTrait;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $modelName = 'accordion';
    
}
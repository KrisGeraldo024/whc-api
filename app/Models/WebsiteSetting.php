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

class WebsiteSetting extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'id'
    ];

    public $timestamps = true;

    protected $modelName = 'website_setting';

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}

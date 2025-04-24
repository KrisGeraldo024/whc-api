<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UuidTrait;

class File extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'created_at'
    ];

    protected $hidden  = [
        'updated_at',
        'deleted_at'
    ];

    // public function getFilenameAttribute ()
    // {
    //     return $this->attributes['path'];
    // }

    // public function getFileresizedAttribute () {
    //     return $this->attributes['path_resized'];
    // }

    // public function getPathAttribute ($value)
    // {
    //      // return url('/') . '/' . $value;
    //     return url('/') . '/storage/' . $value;
    //     //return config('app.aws_mask_url') .($value);
    //    // return config('app.api_url') .($value);
    // }

    // public function getPathResizedAttribute ($value)
    // {
    //      // return url('/') . '/' . $value;
    //     return url('/') . '/storage/' . $value;
    //     //return config('app.aws_mask_url') .($value);
    //     //return config('app.api_url') .($value);
    // }

    protected $appends = array('original_path', 'original_path_resized');

    public function getOriginalPathAttribute()
    {
       // if (isset($this->attributes['path'])) return str_replace(config('app.api_url'), '', $this->attributes['path']);
       return $this->attributes['path'];
    }

    public function getOriginalPathResizedAttribute()
    {
        //if (isset($this->attributes['path_resized'])) return str_replace(config('app.api_url'), '', $this->attributes['path_resized']);
        return $this->attributes['path_resized'];
    }

    public function getFilenameAttribute ()
    {
        return $this->attributes['path'];
    }

    public function getFileresizedAttribute () {
        return $this->attributes['path_resized'];
    }

    public function getPathAttribute ($value)
    {
        //return config('app.api_url') .($value);
        //return '/storage/' . $value;
        return $value;
    }

    public function getPathResizedAttribute ($value)
    {
        // return config('app.api_url') .($value);
        // return '/storage/' . $value;
        return $value;
    }
}
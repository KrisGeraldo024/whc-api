<?php

namespace App\Traits;

use Illuminate\Support\Facades\{
    Http,
    Storage,
    Schema
};
use App\Models\File;

trait FileTrait
{
    public $default_hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function files ()
    {
        $model = $this->modelName;

        return $this->hasMany(File::class, 'parent_id', 'id')
        ->where('model', $model)
        ->whereNull('deleted_at')
        ->orderBy('sequence');
    }

    public function uploadPageFile ($data)
    {
        File::create([
            'parent_id'    => $this->id,
            'model'        => $data['model'],
            'title'        => $data['title'],
            'alt'          => $data['alt'],
            'caption'      => $data['caption'],
            'sequence'     => $data['sequence'],
            'path'         => $data['path'],
            'path_resized' => $data['path_resized'],
            'category'     => $data['category'],
            'name'         => $data['name'],
            'size'         => $data['size'],
        ]);
    }
}
<?php

namespace App\Traits;
use App\Models\Image;

trait ImageTrait
{
    public $default_hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function images ()
    {
        $model = $this->modelName;

        return $this->hasMany(Image::class, 'parent_id', 'id')
            ->where('model', $model)
            ->whereNull('deleted_at')
            ->orderBy('sequence');
    }

    public function uploadImage ($data)
    {
        Image::create([
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
            'link'         => $data['link'],
        ]);
    }
}
<?php

namespace App\Traits;
use App\Models\Metadata;

trait MetadataTrait
{
    public $default_hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function metadata ()
    {
        return $this->hasOne(Metadata::class, 'parent_id', 'id')
        ->whereNull('deleted_at');
    }
}
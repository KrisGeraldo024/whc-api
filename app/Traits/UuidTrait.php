<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait UuidTrait
{
    protected static function bootUuidTrait () {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getKeyType () {
        return 'string';
    }

    public function getIncrementing () {
        return false;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $guarded = [
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    // public function getEditorIdAttribute ($value)
    // {
    //     $user = User::find($value);
    //     return $user->userDetail->full_name;
    // }

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id', 'id');
    }
}

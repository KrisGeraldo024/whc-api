<?php
// app/Models/PaymentMethod.php
namespace App\Models;

use App\Traits\{
    GlobalTrait, 
    FileTrait, 
    UuidTrait,
    ImageTrait,
    MetadataTrait,
};
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo, 
    BelongsToMany, 
    HasMany, 
    HasOne
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes
};

class PaymentMethod extends Model 
{
    use HasFactory, UuidTrait, GlobalTrait, ImageTrait, MetadataTrait, SoftDeletes;

    protected $modelName = 'payment_method';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $fillable = ['title', 'published'];

    public function paymentPlatforms(): HasMany 
    {
        return $this->hasMany(PaymentPlatform::class);
    }
}
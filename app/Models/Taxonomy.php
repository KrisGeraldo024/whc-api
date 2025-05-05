<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};
use App\Traits\{
    ImageTrait,
    UuidTrait
};


class Taxonomy extends Model
{
    use HasFactory, SoftDeletes, UuidTrait, ImageTrait;

    protected $guarded = [
        'id'
    ];

    protected $modelName = 'taxonomy';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'location_type',
        'officer_type',
        'email_recipients',
        'contact_number',
        'email',
        'map_url',
        'address',
        'type',
    ];


    protected $casts = [
        'email_recipients' => 'array'
    ];

    // Constants for taxonomy types
    const TYPE_BRAND = 'brand';
    const TYPE_MATERIAL = 'material';
    const TYPE_TYPE = 'type';
    const TYPE_PURPOSE = 'purpose';
    const TYPE_SIZE = 'size';
    const TYPE_FINISH = 'finish';
    const TYPE_SHADE = 'shade';
    const TYPE_FEATURE = 'feature';
    const TYPE_COLOR = 'color';
    const TYPE_APPLICATION = 'application';
    const TYPE_PROPERTY_LOCATION = 'property_location';
    const TYPE_PROPERTY_STATUS = 'property_status';
    const TYPE_ARTICLE_CATEGORY = 'article_category';
    const TYPE_JOB_LOCATION = 'job_location';
    const TYPE_EMPLOYMENT = 'employment_type';
    const TYPE_INQUIRY = 'inquiry_type';
    const TYPE_AFTER_SALES = 'after_sales_officer';
    const TYPE_OFFICE = 'office_location';
    const TYPE_REFERRAL = 'referral_type';
    const TYPE_PRIORITY_LOCATION = 'priority_location';
    const TYPE_FORM_PAGE = 'form_page';


    // Scope methods for easy querying
    // public function scopeunitTypes($query)
    public function scopeBrand($query)
    {
        return $query->where('type', self::TYPE_BRAND);
    }

    public function scopeMaterial($query)
    {
        return $query->where('type', self::TYPE_MATERIAL);
    }

    public function scopeType($query)
    {
        return $query->where('type', self::TYPE_TYPE);
    }

    public function scopePurpose($query)
    {
        return $query->where('type', self::TYPE_PURPOSE);
    }

    public function scopeSize($query)
    {
        return $query->where('type', self::TYPE_SIZE);
    }

    public function scopeFinish($query)
    {
        return $query->where('type', self::TYPE_FINISH);
    }

    public function scopeShade($query)
    {
        return $query->where('type', self::TYPE_SHADE);
    }

    public function scopeFeature($query)
    {
        return $query->where('type', self::TYPE_FEATURE);
    }

    public function scopeColor($query)
    {
        return $query->where('type', self::TYPE_COLOR);
    }

    public function scopeApplication($query)
    {
        return $query->where('type', self::TYPE_APPLICATION);
    }

    public function scopePropertyLocations($query)
    {
        return $query->where('type', self::TYPE_PROPERTY_LOCATION);
    }
    public function scopePropertyStatuses($query)
    {
        return $query->where('type', self::TYPE_PROPERTY_STATUS);
    }

    public function scopeArticleCategories($query)
    {
        return $query->where('type', self::TYPE_ARTICLE_CATEGORY);
    }

    public function scopeJobLocations($query)
    {
        return $query->where('type', self::TYPE_JOB_LOCATION);
    }

    public function scopeEmploymentTypes($query)
    {
        return $query->where('type', self::TYPE_EMPLOYMENT);
    }

    public function scopeInquiryTypes($query)
    {
        return $query->where('type', self::TYPE_INQUIRY);
    }

    public function scopeAfterSalesOfficers($query)
    {
        return $query->where('type', self::TYPE_AFTER_SALES);
    }

    public function scopeOfficeLocations($query)
    {
        return $query->where('type', self::TYPE_OFFICE);
    }

    public function scopeReferralTypes($query)
    {
        return $query->where('type', self::TYPE_REFERRAL);
    }
    
    public function scopePriorityLocations($query)
    {
        return $query->where('type', self::TYPE_PRIORITY_LOCATION);
    }

    public function scopeFormPages($query)
    {
        return $query->where('type', self::TYPE_FORM_PAGE);
    }

    public function properties() : HasMany
    {
        return $this->hasMany(Property::class, 'location_id', 'id');
    }
    public function propertyStatus() : HasMany
    {
        return $this->hasMany(Property::class, 'status_id', 'id');
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }

    public function units() : HasMany
    {
        return $this->hasMany(Unit::class, 'unit_type', 'id');
    }
    public function job_type() : HasMany
    {
        return $this->hasMany(Career::class, 'employment_type_id', 'id');
    }
    public function job_location() : HasMany
    {
        return $this->hasMany(Career::class, 'location_id', 'id');
    }
}

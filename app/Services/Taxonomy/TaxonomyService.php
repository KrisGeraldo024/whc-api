<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy;

class TaxonomyService
{
    /**
     * Get unit types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getUnitTypes($sortBy = 'created_at', $sortDirection = 'desc', $all = false, $propertyType = '')
    {
        return Taxonomy::unitTypes()->orderBy($sortBy, $sortDirection)
            ->withCount('units')
            ->when($all, function ($q) use ($propertyType) {
                return $q->where('property_type', $propertyType === 'house-and-lots' ? 'House & Lot' : 'Condominium')->get();
            }, function ($q) {
                return $q->paginate(10);
            });
    }

    public function getBrand($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Brand()->orderBy($sortBy, $sortDirection);
        
        // Remove the withCount('brand') as there's no relationship method
        // named 'brand' in your Taxonomy model
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }


    public function getType($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Type()->orderBy($sortBy, $sortDirection);
        
        // Remove the withCount('brand') as there's no relationship method
        // named 'brand' in your Taxonomy model
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getPurpose($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Purpose()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getSize($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Size()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getFinish($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Finish()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getShade($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Shade()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }


    public function getFeature($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Feature()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getColor($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Color()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    public function getApplication($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        $query = Taxonomy::Application()->orderBy($sortBy, $sortDirection);
        
        if ($all) {
            return $query->paginate(10);
        }
        
        return $query->get();
    }

    /**
     * Get office locations with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getOfficeLocations($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::officeLocations()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

    /**
     * Get property statuses with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getPropertyStatuses($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        return Taxonomy::propertyStatuses()
            ->orderBy($sortBy, $sortDirection)
            ->withCount('propertyStatus')
            ->when($all, function ($q) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(10);
            });
    }

    /**
     * Get property locations with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getPropertyLocations($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        return Taxonomy::propertyLocations()
            ->orderBy($sortBy, $sortDirection)
            ->with('images')
            ->withCount('properties')
            ->when($all, function ($q) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(10);
            });
    }

    /**
     * Get inquiry types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getInquiryTypes($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::inquiryTypes()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

     /**
     * Get inquiry types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getFormPages($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::formPages()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

    /**
     * Get article categories with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getArticleCategories($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        return Taxonomy::articleCategories()->orderBy($sortBy, $sortDirection)
            ->withCount('articles')
            ->when($all, function ($q) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(10);
            });
    }

    /**
     * Get employment types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getEmploymentTypes($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        return Taxonomy::employmentTypes()->orderBy($sortBy, $sortDirection)
            ->withCount('job_type')
            ->when($all, function ($q) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(10);
            });;
    }

    /**
     * Get job locations with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getJobLocations($sortBy = 'created_at', $sortDirection = 'desc', $all = false)
    {
        return Taxonomy::jobLocations()->orderBy($sortBy, $sortDirection)
            ->withCount('job_location')
            ->when($all, function ($q) {
                return $q->get();
            }, function ($q) {
                return $q->paginate(10);
            });
    }

    /**
     * Get after sales officers with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getAfterSalesOfficers($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::afterSalesOfficers()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

    /**
     * Get contact us data including after-sales officers and office locations
     *
     * @return array
     */
    public function getContactUsData()
    {
        // Fetch after-sales officers
        $afterSalesOfficers = Taxonomy::afterSalesOfficers()
            ->orderBy('name', 'asc')
            ->get();

        // Fetch office locations
        $officeLocations = Taxonomy::officeLocations()
            ->orderBy('created_at', 'desc')
            ->get();

        // Categorize office locations
        $categorizedOffices = [
            'main_office' => [],
            'regional_office' => []
        ];

        // Separate main and regional offices
        foreach ($officeLocations as $office) {
            if ($office->location_type === 'Main Office') {
                $categorizedOffices['main_office'][] = $office;
            } elseif ($office->location_type === 'Site Office') {
                $categorizedOffices['site_office'][] = $office;
            }
        }

        // Return combined data
        return [
            'after_sales_officers' => $afterSalesOfficers,
            'office_locations' => $categorizedOffices
        ];
    }

    /**
     * Get referral types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getReferralTypes($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::referralTypes()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

    /**
     * Get referral types with pagination and sorting
     *
     * @param string $sortBy
     * @param string $sortDirection
     * @return mixed
     */
    public function getPriorityLocations($sortBy = 'created_at', $sortDirection = 'desc')
    {
        return Taxonomy::priorityLocations()->orderBy($sortBy, $sortDirection)->paginate(10);
    }

    /**
     * Get all referral types sorted alphabetically by name
     *
     * @return mixed
     */
    public function getReferralTypesWeb()
    {
        return Taxonomy::referralTypes()->orderBy('name', 'asc')->get();
    }

    /**
     * Get all referral types sorted alphabetically by name
     *
     * @return mixed
     */
    public function getPriorityLocationsWeb()
    {
        return Taxonomy::priorityLocations()->orderBy('name', 'asc')->get();
    }

       /**
     * Get all referral types sorted alphabetically by name
     *
     * @return mixed
     */
    public function getInquiryTypesWeb()
    {
        return Taxonomy::inquiryTypes()->orderBy('name', 'asc')->get();
    }


    // In TaxonomyService.php
    public function getTaxonomy(int $id)
    {
        return Taxonomy::findOrFail($id);
    }
}

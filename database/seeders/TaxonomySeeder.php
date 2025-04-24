<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run()
    {
        // Unit Types
        $unitTypes = [
            ['name' => 'House & Lot', 'type' => Taxonomy::TYPE_UNIT],
            ['name' => 'Condominium', 'type' => Taxonomy::TYPE_UNIT],
        ];

        // Property Locations
        $propertyLocations = [
            ['name' => 'Mandaluyong', 'type' => Taxonomy::TYPE_PROPERTY_LOCATION],
            ['name' => 'Davao', 'type' => Taxonomy::TYPE_PROPERTY_LOCATION],
            ['name' => 'Baguio', 'type' => Taxonomy::TYPE_PROPERTY_LOCATION],
        ];

        // Property Locations
        $propertyStatuses = [
            ['name' => 'Sold Out', 'type' => Taxonomy::TYPE_PROPERTY_STATUS],
            ['name' => 'Pre-Selling', 'type' => Taxonomy::TYPE_PROPERTY_STATUS],
            ['name' => 'Few units Left', 'type' => Taxonomy::TYPE_PROPERTY_STATUS],
        ];

        // Article Categories
        $articleCategories = [
            ['name' => 'News', 'type' => Taxonomy::TYPE_ARTICLE_CATEGORY],
            ['name' => 'Home Stories', 'type' => Taxonomy::TYPE_ARTICLE_CATEGORY],
            ['name' => 'Blogs', 'type' => Taxonomy::TYPE_ARTICLE_CATEGORY],
        ];

        // Job Locations
        $jobLocations = [
            ['name' => 'Taguig', 'type' => Taxonomy::TYPE_JOB_LOCATION],
        ];

        // Employment Types
        $employmentTypes = [
            ['name' => 'Full Time', 'type' => Taxonomy::TYPE_EMPLOYMENT],
            ['name' => 'Part Time', 'type' => Taxonomy::TYPE_EMPLOYMENT],
            ['name' => 'Project Based', 'type' => Taxonomy::TYPE_EMPLOYMENT],
            ['name' => 'Internship', 'type' => Taxonomy::TYPE_EMPLOYMENT],
        ];

        // Inquiry Types
        $inquiryTypes = [
            [
                'name' => 'General Inquiries',
                'type' => Taxonomy::TYPE_INQUIRY,
                'email_recipients' => ['sales@companyabc.com', 'hello@companyabc.com']
            ],
        ];

        // After Sales Officers
        $afterSalesOfficers = [
            [
                'first_name' => 'Kleth Anne',
                'last_name' => 'Zamora',
                'officer_type' => 'Horizontal Projects (House and Lot)',
                'type' => Taxonomy::TYPE_AFTER_SALES,
                'contact_number' => '09423622699',
                'email' => 'km.zamora@suntrust.com.ph'
            ],
        ];

        // Process After Sales Officers data to add combined name
        foreach ($afterSalesOfficers as $key => $officer) {
            if (isset($officer['first_name'], $officer['last_name'])) {
                $afterSalesOfficers[$key]['name'] = trim($officer['first_name'] . ' ' . $officer['last_name']);
            }
        }

        // Office Locations
        $officeLocations = [
            ['name' => 'Suntrust Solana Showroom', 'location_type' => 'Main Office', 'address' => '434 Sample Street Suntrust', 'contact_number' => '09423622699',  'map_url' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3827.079618838833!2d120.62249577460692!3d16.420782279931732!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3391a53ded1c4ca1%3A0xc2155f68086992c4!2sSuntrust%2088%20Gibraltar!5e0!3m2!1sen!2sph!4v1729923014520!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'type' => Taxonomy::TYPE_OFFICE],
        ];

        // Referral Types
        $referralTypes = [
            ['name' => 'Internet/Website', 'type' => Taxonomy::TYPE_REFERRAL],
        ];

        // Combine all taxonomies
        $allTaxonomies = array_merge(
            $unitTypes,
            $propertyLocations,
            $propertyStatuses,
            $articleCategories,
            $jobLocations,
            $employmentTypes,
            $inquiryTypes,
            $afterSalesOfficers,
            $officeLocations,
            $referralTypes
        );

        // Insert all taxonomies
        foreach ($allTaxonomies as $taxonomy) {
            Taxonomy::create($taxonomy);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $pages): array
    {
        $pageSections = [];

        foreach ($pages as $page) {
            // Add sections for each page, using the page's UUID as foreign key
            $pageSections[] = [
                'id' => (string) Str::uuid(),
                'page_id' => $page['id'], // Link to page
                'name' => 'Top Banner',
                'title' => 'Sample',
                'description' => '',
                'order' => 1,
                'has_button' => $page['identifier'] === 'homepage' ? 1 : 0
            ];
            if($page['identifier'] === 'homepage') {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Featured House & Lots',
                    'title' => 'Spacious living awaits you.',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Featured Condominiums',
                    'title' => 'Experience urban living at its finest.',
                    'description' => '',
                    'order' => 3,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Browse by Locations',
                    'title' => 'Find Your Ideal Property Nationwide',
                    'description' => '',
                    'order' => 4,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'About Us',
                    'title' => 'Over 20 Years of Building Quality Homes for Filipinos',
                    'description' => '',
                    'order' => 5,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Payment Channel CTA',
                    'title' => 'Payments made easy and secure!                    ',
                    'description' => '',
                    'order' => 6,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Get a Quote CTA',
                    'title' => 'Get In Touch With Us',
                    'description' => '',
                    'order' => 7,
                    'has_button' => 1
                ];
            }
            if($page['identifier'] === 'house-and-lots') {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Get a Quote Banner',
                    'title' => 'Ready to find your dream property?',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 1
                ];
            }
            if( $page['identifier'] === 'condominiums') {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Get a Quote Banner',
                    'title' => 'Ready to find your dream property?',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 1
                ];
            }
            if($page['identifier'] === 'homebuyers-guide') {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Steps',
                    'title' => 'Step by Step Guide to Buying a Property',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Buyer's Portal Banner",
                    'title' => 'Exclusive Buyer’s Hub Just for You',
                    'description' => '',
                    'order' => 3,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Payment Channel CTA",
                    'title' => 'Payments made easy and secure!',
                    'description' => '',
                    'order' => 4,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Get a Quote CTA",
                    'title' => 'Ready to find your dream property?',
                    'description' => '',
                    'order' => 5,
                    'has_button' => 1
                ];
            }
            if($page['identifier'] === 'about-us') {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => 'Company Profile',
                    'title' => 'Company Profile',
                    'description' => 'Suntrust Properties, Inc. (SPI) is a 100% wholly-owned subsidiary of Megaworld Corporation, a company under the umbrella of the Alliance Global Group, Inc.',
                    'order' => 2,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Mission",
                    'title' => 'mission',
                    'description' => 'Our goal is to become the benchmark in affordable township development, setting the highest standards of quality and value. We aim to be the premier choice for buyers, offering exceptional communities that meet their needs and aspirations.',
                    'order' => 3,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Vision",
                    'title' => 'vision',
                    'description' => 'This mission is being accomplished through the dedication of a highly motivated workforce and the strategic direction of a dynamic management team. Together, they are committed to fostering teamwork, upholding the highest standards of professionalism, and adhering to principles of corporate social responsibility.',
                    'order' => 4,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Other Property Development",
                    'title' => 'Browse Our Other Developments',
                    'description' => '',
                    'order' => 5,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Suntrust Officers",
                    'title' => 'Our Leadership Team',
                    'description' => '',
                    'order' => 6,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Awards",
                    'title' => 'Latest Awards and Recognitions',
                    'description' => '',
                    'order' => 6,
                    'has_button' => 0
                ];
            }
            if($page['identifier'] === "sellers-guide") {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Agent's Portal",
                    'title' => "Exclusive Access for Suntrust Agents",
                    'description' => '',
                    'order' => 2,
                    'has_button' => 1
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "In-house Sales Group",
                    'title' => 'Become a Suntrustee',
                    'description' => '',
                    'order' => 3,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Business Partner Network",
                    'title' => 'Be a Suntrust Real Estate Broker',
                    'description' => '',
                    'order' => 4,
                    'has_button' => 0
                ];
            }
            if($page['identifier'] === "news-and-articles") {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Latest News",
                    'title' => 'Latest News & Articles',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 0
                ];
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Instagram",
                    'title' => 'Follow us on Instagram',
                    'description' => '',
                    'order' => 3,
                    'has_button' => 1
                ];
            }
            // if($page['identifier'] === "careers") {
            //     $pageSections[] = [
            //         'id' => (string) Str::uuid(),
            //         'page_id' => $page['id'],
            //         'name' => "Benefits",
            //         'title' => 'Sample Title',
            //         'description' => '',
            //         'order' => 2,
            // 'has_button' => 0
            //     ];
            //     $pageSections[] = [
            //         'id' => (string) Str::uuid(),
            //         'page_id' => $page['id'],
            //         'name' => "Employee Activities",
            //         'title' => 'Sample Title',
            //         'description' => '',
            //         'order' => 3,
            // 'has_button' => 0
            //     ];
            //     $pageSections[] = [
            //         'id' => (string) Str::uuid(),
            //         'page_id' => $page['id'],
            //         'name' => "Main CTA",
            //         'title' => 'Sample Title',
            //         'description' => '',
            //         'order' => 4,
            // 'has_button' => 0
            //     ];
            //     $pageSections[] = [
            //         'id' => (string) Str::uuid(),
            //         'page_id' => $page['id'],
            //         'name' => "Contact CTA",
            //         'title' => 'Sample Title',
            //         'description' => '',
            //         'order' => 5,
            // 'has_button' => 0
            //     ];
            // }
            
            if($page['identifier'] === "contact-us") {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "After Sales Officers",
                    'title' => 'Get In Touch With Us',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 0
                ];
                // $pageSections[] = [
                //     'id' => (string) Str::uuid(),
                //     'page_id' => $page['id'],
                //     'name' => "Main Offices",
                //     'title' => 'Sample Title',
                //     'description' => '',
                //     'order' => 3,
                //     'has_button' => 0
                // ];
                // $pageSections[] = [
                //     'id' => (string) Str::uuid(),
                //     'page_id' => $page['id'],
                //     'name' => "Regional Offices",
                //     'title' => 'Sample Title',
                //     'description' => '',
                //     'order' => 4,
                //     'has_button' => 0
                // ];
            }
            if($page['identifier'] === "get-a-quote") {
                $pageSections[] = [
                    'id' => (string) Str::uuid(),
                    'page_id' => $page['id'],
                    'name' => "Contact Us CTA",
                    'title' => 'Ready to find your dream property?',
                    'description' => '',
                    'order' => 2,
                    'has_button' => 1
                ];
            }
            // Add more sections as necessary
        }

        DB::table('page_sections')->insert($pageSections);

        return $pageSections;
    }
}
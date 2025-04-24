<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(array $pages, array $pageSections): void
    {
        $buttons = [];

        foreach ($pageSections as $section) {
            foreach ($pages as $page) {
                if ($section['name'] == 'Top Banner' && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'House & Lots',
                        'link' => '/house-and-lot',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Condominiums',
                        'link' => '/condominium',
                        'is_link_out' => 0,
                        'order' => 2
                    ];
                }
                if (($section['name'] == 'Featured House & Lots') && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Explore House & Lots',
                        'link' => '/house-and-lots',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if (($section['name'] == 'Featured Condominiums') && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Explore Condominiums',
                        'link' => '/condominium',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == 'About Us' && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Explore About Us',
                        'link' => '/about-us',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == 'Payment Channel CTA' && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Explore Payment Channel',
                        'link' => '/payment-channels',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == 'Get a Quote CTA' && $page['identifier'] == 'homepage') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Get a Quote Today',
                        'link' => '/get-quote',
                        'is_link_out' => 0, 
                        'order' => 1
                    ];
                }
                if ($section['name'] == 'Get a Quote Banner' && $page['identifier'] === 'condominiums') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Get a Quote Today',
                        'link' => '/get-quote',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == 'Get a Quote Banner' && $page['identifier'] === 'house-and-lots') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Get a Quote Today',
                        'link' => '/get-quote',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == "Buyer's Portal Banner" && $page['identifier'] === 'homebuyers-guide') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => "Explore Buyer's Portal",
                        'link' => '/sample-link',
                        'is_link_out' => 1,
                        'order' => 1
                    ];
                }
                if ($section['name'] == "Payment Channel CTA" && $page['identifier'] == 'homebuyers-guide') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Sample Button 1',
                        'link' => '/sample-link',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
                if ($section['name'] == "Agent's Portal" && $page['identifier'] == 'sellers-guide') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => "Explore Buyer's Portal",
                        'link' => '/sample-link',
                        'is_link_out' => 1,
                        'order' => 1
                    ];
                }
                if ($section['name'] == "Instagram" && $page['identifier'] == 'news-and-articles') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Follow us on Instagram',
                        'link' => '/instagram.com',
                        'is_link_out' => 1,
                        'order' => 1
                    ];
                }
                if ($section['name'] == "Contact Us CTA" && $page['identifier'] == 'get-a-quote') {
                    $buttons[] = [ 
                        'id' => (string) Str::uuid(),
                        'parent' => $section['id'], // Link to page_section
                        'button_name' => 'Contact Us',
                        'link' => '/contact-us',
                        'is_link_out' => 0,
                        'order' => 1
                    ];
                }
            }
        }
        DB::table('buttons')->insert($buttons);
    }
}

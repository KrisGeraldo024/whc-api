<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): array
    {
        $pages = [
            [
                'id' => (string) Str::uuid(),
                'name'  => 'Homepage',
                'slug' => 'homepage',
                'identifier' => 'homepage',
                'category' => 'Homepage',
                'order' => 1,
                'page_parent' => 1,
                'page_parent_seq' => 'A1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => 'House & Lots',
                'slug' => 'house-and-lots',
                'identifier' => 'house-and-lots',
                'category' => 'Our Properties',
                'order' => 2,
                'page_parent' => 1,
                'page_parent_seq' => 'B1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => 'Condominiums',
                'slug' => 'condominiums',
                'identifier' => 'condominiums',
                'category' => 'Our Properties',
                'order' => 3,
                'page_parent' => 1,
                'page_parent_seq' => 'B2'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Homebuyer's Guide",
                'slug' => 'homebuyers-guide',
                'identifier' => 'homebuyers-guide',
                'category' => "Homebuyer's Guide",
                'order' => 4,
                'page_parent' => 1,
                'page_parent_seq' => 'C1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "About Us",
                'slug' => 'about-us',
                'identifier' => 'about-us',
                'category' => "About Us",
                'order' => 5,
                'page_parent' => 1,
                'page_parent_seq' => 'D1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Seller's guide",
                'slug' => 'sellers-guide',
                'identifier' => 'sellers-guide',
                'category' => "Seller's Guide",
                'order' => 6,
                'page_parent' => 1,
                'page_parent_seq' => 'E1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Payment Channel",
                'slug' => 'payment-channel',
                'identifier' => 'payment-channel',
                'category' => "Payment Channel",
                'order' => 7,
                'page_parent' => 1,
                'page_parent_seq' => 'F1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "News & Articles",
                'slug' => 'news-and-articles',
                'identifier' => 'news-and-articles',
                'category' => "News & Articles",
                'order' => 8,
                'page_parent' => 1,
                'page_parent_seq' => 'G1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Corporate Careers",
                'slug' => 'careers',
                'identifier' => 'careers',
                'category' => "Careers",
                'order' => 9,
                'page_parent' => 1,
                'page_parent_seq' => 'H1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Contact Us",
                'slug' => 'contact-us',
                'identifier' => 'contact-us',
                'category' => "Contact Us",
                'order' => 10,
                'page_parent' => 1,
                'page_parent_seq' => 'I1'
            ],
            [
                'id' => (string) Str::uuid(),
                'name'  => "Get a Quote",
                'slug' => 'get-a-quote',
                'identifier' => 'get-a-quote',
                'category' => "Get a Quote",
                'order' => 11,
                'page_parent' => 1,
                'page_parent_seq' => 'J1'
            ],
        ];

        DB::table('pages')->insert($pages);

        // Pass the UUIDs to PageSectionSeeder (if you want to do this in one go)
        // $this->call(PageSectionsSeeder::class, false, ['pages' => $pages]);
        
        return $pages;
    }
}
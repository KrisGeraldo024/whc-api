<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Email;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Email::factory()->count(12)->create();
        User::factory()->count(1)->create();
        $pageSeeder = new PageSeeder();
        $pages = $pageSeeder->run();

        // Step 2: Run PageSectionsSeeder and pass $pages, get the $pageSections array
        $pageSectionsSeeder = new PageSectionsSeeder();
        $pageSections = $pageSectionsSeeder->run($pages);

        // Step 3: Run ButtonSeeder and pass both $pages and $pageSections
        $buttonSeeder = new ButtonSeeder();
        $buttonSeeder->run($pages, $pageSections);

        $this->call(PaymentMethodSeeder::class);
        $this->call(PaymentPlatformSeeder::class);
        $this->call([
            TaxonomySeeder::class,
        ]);
    }
}

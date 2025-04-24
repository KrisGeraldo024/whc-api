<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            'Online Payment',
            'Over the Counter',
            'Overseas Transaction',
            'Auto Debit',
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert([
                'id' => Str::uuid(),
                'title' => $method,
                'published' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

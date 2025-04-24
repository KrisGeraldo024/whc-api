<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'title' => 'Aqwire',
                'steps' => [
                    [
                        'title' => 'Create Account',
                        'description' => 'Sign up for an Aqwire account using your email address'
                    ],
                    [
                        'title' => 'Verify Identity',
                        'description' => 'Complete the KYC process by submitting required documents'
                    ],
                    [
                        'title' => 'Link Bank Account',
                        'description' => 'Connect your preferred bank account for transactions'
                    ]
                ]
            ],
            [
                'title' => 'BDO',
                'steps' => [
                    [
                        'title' => 'Access Online Banking',
                        'description' => 'Log in to your BDO Online Banking account'
                    ],
                    [
                        'title' => 'Select Payment Option',
                        'description' => 'Choose "Bills Payment" from the main menu'
                    ],
                    [
                        'title' => 'Complete Transaction',
                        'description' => 'Enter payment details and confirm the transaction'
                    ]
                ]
            ],
            [
                'title' => 'BPI',
                'steps' => [
                    [
                        'title' => 'Login to BPI Online',
                        'description' => 'Access your BPI Online Banking account'
                    ],
                    [
                        'title' => 'Navigate to Payments',
                        'description' => 'Select "Transfer Money" from the dashboard'
                    ],
                    [
                        'title' => 'Confirm Payment',
                        'description' => 'Review and authorize the payment transaction'
                    ]
                ]
            ],
            [
                'title' => 'Hello Money',
                'steps' => [
                    [
                        'title' => 'Download App',
                        'description' => 'Install the Hello Money mobile application'
                    ],
                    [
                        'title' => 'Register Account',
                        'description' => 'Complete the registration process with your details'
                    ],
                    [
                        'title' => 'Fund Account',
                        'description' => 'Add funds to your Hello Money wallet'
                    ]
                ]
            ],
            [
                'title' => 'Bux',
                'steps' => [
                    [
                        'title' => 'Setup Bux Account',
                        'description' => 'Create and verify your Bux payment account'
                    ],
                    [
                        'title' => 'Link Payment Method',
                        'description' => 'Add your preferred payment method to Bux'
                    ],
                    [
                        'title' => 'Initiate Transfer',
                        'description' => 'Start the payment transfer process'
                    ]
                ]
            ],
            [
                'title' => 'AUB',
                'steps' => [
                    [
                        'title' => 'Access AUB Portal',
                        'description' => 'Log in to your AUB online banking account'
                    ],
                    [
                        'title' => 'Select Service',
                        'description' => 'Choose the appropriate payment service option'
                    ],
                    [
                        'title' => 'Process Payment',
                        'description' => 'Enter payment details and submit for processing'
                    ]
                ]
            ]
        ];
        $sequence = 1; 
        foreach ($platforms as $platform) {
            // You'll need to first get a payment_method_id
            // This assumes you have a payment method already seeded
            $payment_method_id = DB::table('payment_methods')->first()->id;
            DB::table('payment_platforms')->insert([
                'id' => Str::uuid(),
                'payment_method_id' => $payment_method_id,
                'title' => $platform['title'],
                'sequence' => $sequence++,
                'steps' => json_encode($platform['steps']),
                'buttonText' => 'Pay with ' . $platform['title'],
                'buttonLink' => '#', // Placeholder link
                'buttonActive' => 'false', // All buttons disabled as requested
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
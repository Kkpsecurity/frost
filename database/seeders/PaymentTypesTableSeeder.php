<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'id' => 1,
                'is_active' => true,
                'name' => 'PayFlowPro',
                'model_class' => '\\App\\Models\\Payments\\PayFlowPro',
                'controller_class' => '\\App\\Http\\Controllers\\Payments\\PayFlowProController'
            ],
            [
                'id' => 2,
                'is_active' => false,
                'name' => 'PayPal',
                'model_class' => '\\App\\Models\\Payments\\PayPal',
                'controller_class' => '\\App\\Http\\Controllers\\Payments\\PayPalController'
            ]
        ];

        // Clear existing data
        DB::table('payment_types')->truncate();

        // Insert new data
        foreach ($paymentTypes as $paymentType) {
            DB::table('payment_types')->insert($paymentType);
        }

        echo "Seeded " . count($paymentTypes) . " payment types successfully.\n";
    }
}

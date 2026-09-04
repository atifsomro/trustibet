<?php

namespace Database\Seeders;

use App\Models\InvestmentPackage;
use Illuminate\Database\Seeder;

class InvestmentPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'BASIC',
                'price' => 10.00,
                'daily_roi' => 0.50,
                'total_days' => 30,
                'description' => "Maintenance Fee: 2.5%\n24/7 Support",
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'PRO',
                'price' => 50.00,
                'daily_roi' => 2.00,
                'total_days' => 60,
                'description' => "Maintenance Fee: 2%\nPriority Support",
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'PREMIUM',
                'price' => 100.00,
                'daily_roi' => 5.00,
                'total_days' => 90,
                'description' => "Maintenance Fee: 1%\nVIP Support",
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            InvestmentPackage::query()->updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}

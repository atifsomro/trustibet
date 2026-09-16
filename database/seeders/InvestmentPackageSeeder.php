<?php

namespace Database\Seeders;

use App\Models\InvestmentPackage;
use Illuminate\Database\Seeder;

class InvestmentPackageSeeder extends Seeder
{
    public function run(): void
    {
        $fiveYears = 365 * 5; // 1825 days

        $packages = [
            [
                'name' => 'BABY',
                'price' => 10.00,
                'daily_roi' => 0.027,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.027 to $0.20',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'STAND',
                'price' => 20.00,
                'daily_roi' => 0.054,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.054 to $0.50',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'S90 PRO',
                'price' => 40.00,
                'daily_roi' => 0.10,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.10 to $1',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'LITE',
                'price' => 50.00,
                'daily_roi' => 0.13,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.13 to $2',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'STANDARD',
                'price' => 100.00,
                'daily_roi' => 0.28,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.28 to $5',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'ELITE',
                'price' => 250.00,
                'daily_roi' => 0.68,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $0.68 to $15',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'SUPER',
                'price' => 500.00,
                'daily_roi' => 1.37,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $1.37 to $40',
                    'Maintenance Fee: 2.5%',
                    'One Time Deposit Fee',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'PRO',
                'price' => 1000.00,
                'daily_roi' => 2.70,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $2.7 to $80',
                    '10% Deposit Bonus',
                    'Maintenance Fee: 2.5%',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'PRO PLUS',
                'price' => 2500.00,
                'daily_roi' => 6.80,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $6.8 to $150',
                    '50% Deposit Bonus',
                    'Maintenance Fee: 2.5%',
                    '24/7 Customer Support',
                    'Instant Withdrawal',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'BUSINESS',
                'price' => 5000.00,
                'daily_roi' => 13.60,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $13.6 to $350',
                    '200% Deposit Bonus',
                    'Highest Daily Earnings',
                    'Ultra Fast Miner, Trading Bots',
                    'Return Principal Amount After 5 Years',
                ],
                'is_active' => true,
                'is_recommended' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'BUSINESS PRO',
                'price' => 10000.00,
                'daily_roi' => 27.30,
                'total_days' => $fiveYears,
                'feature_points' => [
                    'Daily ROI: $27.3 to $900',
                    '300% Deposit Bonus',
                    'Highest Daily Earnings',
                    'Ultra Fast Miner, Trading Bots',
                    'Return Principal Amount After 5 Years',
                ],
                'is_active' => true,
                'is_recommended' => true,
                'sort_order' => 11,
            ],
        ];

        $keepNames = [];

        foreach ($packages as $package) {
            $keepNames[] = $package['name'];

            InvestmentPackage::query()->updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        // Deactivate legacy packages that are no longer in the plan list.
        InvestmentPackage::query()
            ->whereNotIn('name', $keepNames)
            ->update(['is_active' => false]);
    }
}

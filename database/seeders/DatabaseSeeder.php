<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLog;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $employees = collect([
            [
                'name' => 'Maya Chen',
                'email' => 'maya.chen@northstar.dev',
                'designation' => 'Senior Full Stack Developer',
                'department' => 'Engineering',
            ],
            [
                'name' => 'Omar Farouk',
                'email' => 'omar.farouk@northstar.dev',
                'designation' => 'Engineering Lead',
                'department' => 'Engineering',
            ],
            [
                'name' => 'Priya Nair',
                'email' => 'priya.nair@northstar.dev',
                'designation' => 'DevOps Engineer',
                'department' => 'Platform',
            ],
            [
                'name' => 'Ethan Brooks',
                'email' => 'ethan.brooks@northstar.dev',
                'designation' => 'QA Engineer',
                'department' => 'Quality Assurance',
            ],
        ])->mapWithKeys(fn (array $attributes) => [
            $attributes['email'] => Employee::firstOrCreate(
                ['email' => $attributes['email']],
                $attributes,
            ),
        ]);

        $categories = collect([
            'Laptops',
            'Monitors',
            'Keyboards',
            'Networking Gear',
            'Servers',
        ])->mapWithKeys(fn (string $name) => [
            $name => AssetCategory::firstOrCreate(['name' => $name]),
        ]);

        $assets = [
            [
                'asset_tag' => 'IT-LAP-001',
                'category' => 'Laptops',
                'model_name' => 'MacBook Pro 14-inch M3 Pro',
                'serial_number' => 'C02M3P001XYZ',
                'specifications' => '18GB RAM, 512GB SSD, Apple M3 Pro',
                'purchase_date' => '2025-02-14',
                'purchase_cost' => 1999.00,
                'status' => 'assigned',
                'employee' => 'maya.chen@northstar.dev',
                'history' => [['Assigned', 'Issued for full-stack development.', 'maya.chen@northstar.dev']],
            ],
            [
                'asset_tag' => 'IT-LAP-002',
                'category' => 'Laptops',
                'model_name' => 'Lenovo ThinkPad X1 Carbon Gen 12',
                'serial_number' => 'PF4X1C002ABC',
                'specifications' => '32GB RAM, 1TB SSD, Intel Core Ultra 7',
                'purchase_date' => '2025-03-03',
                'purchase_cost' => 1849.00,
                'status' => 'assigned',
                'employee' => 'omar.farouk@northstar.dev',
                'history' => [['Assigned', 'Issued to engineering team lead.', 'omar.farouk@northstar.dev']],
            ],
            [
                'asset_tag' => 'IT-LAP-003',
                'category' => 'Laptops',
                'model_name' => 'Dell Latitude 7440',
                'serial_number' => 'DL7440003QWE',
                'specifications' => '16GB RAM, 512GB SSD, Intel Core i7',
                'purchase_date' => '2025-01-21',
                'purchase_cost' => 1399.00,
                'status' => 'assigned',
                'employee' => 'priya.nair@northstar.dev',
                'history' => [['Assigned', 'Issued for platform and infrastructure work.', 'priya.nair@northstar.dev']],
            ],
            [
                'asset_tag' => 'IT-MON-001',
                'category' => 'Monitors',
                'model_name' => 'Dell UltraSharp 27 4K',
                'serial_number' => 'CN0U2725MON1',
                'specifications' => '27-inch IPS, 4K UHD, USB-C hub',
                'purchase_date' => '2025-04-11',
                'purchase_cost' => 579.00,
                'status' => 'available',
                'employee' => null,
                'history' => [['Returned', 'Returned to the equipment pool after project completion.', 'ethan.brooks@northstar.dev']],
            ],
            [
                'asset_tag' => 'IT-MON-002',
                'category' => 'Monitors',
                'model_name' => 'LG UltraFine 27UP850',
                'serial_number' => 'LG27UP8500002',
                'specifications' => '27-inch IPS, 4K UHD, HDR400',
                'purchase_date' => '2025-05-08',
                'purchase_cost' => 449.00,
                'status' => 'available',
                'employee' => null,
                'history' => [],
            ],
            [
                'asset_tag' => 'IT-LAP-004',
                'category' => 'Laptops',
                'model_name' => 'MacBook Pro 14-inch M2 Pro',
                'serial_number' => 'C02M2P004DEF',
                'specifications' => '16GB RAM, 512GB SSD, Apple M2 Pro',
                'purchase_date' => '2024-06-19',
                'purchase_cost' => 1799.00,
                'status' => 'maintenance',
                'employee' => null,
                'history' => [['Sent to Repair', 'Keyboard service and hardware diagnostics in progress.', null]],
            ],
        ];

        foreach ($assets as $attributes) {
            $employee = $attributes['employee']
                ? $employees->get($attributes['employee'])
                : null;
            $asset = Asset::firstOrCreate(
                ['asset_tag' => $attributes['asset_tag']],
                [
                    'category_id' => $categories->get($attributes['category'])->id,
                    'assigned_to' => $employee?->id,
                    'model_name' => $attributes['model_name'],
                    'serial_number' => $attributes['serial_number'],
                    'specifications' => $attributes['specifications'],
                    'purchase_date' => $attributes['purchase_date'],
                    'purchase_cost' => $attributes['purchase_cost'],
                    'status' => $attributes['status'],
                ],
            );

            foreach ($attributes['history'] as [$action, $remarks, $employeeEmail]) {
                AssetLog::firstOrCreate(
                    [
                        'asset_id' => $asset->id,
                        'employee_id' => $employeeEmail ? $employees->get($employeeEmail)?->id : null,
                        'action' => $action,
                        'remarks' => $remarks,
                    ],
                );
            }
        }
    }
}

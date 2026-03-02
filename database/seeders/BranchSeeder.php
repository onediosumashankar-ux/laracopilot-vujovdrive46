<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Tenant;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $branchData = [
            // TechFlow Solutions Pvt. Ltd. – Bengaluru (HQ) + 3 branches
            [
                'name'         => 'Bengaluru – Koramangala (Head Office)',
                'code'         => 'BLR-HQ',
                'address'      => '4th Floor, Prestige Tech Park, Outer Ring Road',
                'city'         => 'Bengaluru',
                'state'        => 'Karnataka',
                'pincode'      => '560103',
                'phone'        => '+91-80-4567-8900',
                'email'        => 'blr.hq@techflowsolutions.in',
                'manager_name' => 'Vikram Reddy',
                'is_head_office' => true,
                'is_active'    => true,
            ],
            [
                'name'         => 'Mumbai – Andheri East',
                'code'         => 'MUM-AND',
                'address'      => '8th Floor, Techniplex Complex, S.V. Road',
                'city'         => 'Mumbai',
                'state'        => 'Maharashtra',
                'pincode'      => '400069',
                'phone'        => '+91-22-6789-1234',
                'email'        => 'mum.branch@techflowsolutions.in',
                'manager_name' => 'Sneha Kulkarni',
                'is_head_office' => false,
                'is_active'    => true,
            ],
            [
                'name'         => 'Pune – Hinjewadi IT Park',
                'code'         => 'PUN-HIN',
                'address'      => 'Block B2, Rajiv Gandhi Infotech Park, Phase 1',
                'city'         => 'Pune',
                'state'        => 'Maharashtra',
                'pincode'      => '411057',
                'phone'        => '+91-20-6789-5678',
                'email'        => 'pun.branch@techflowsolutions.in',
                'manager_name' => 'Priya Nair',
                'is_head_office' => false,
                'is_active'    => true,
            ],
            [
                'name'         => 'Hyderabad – HITEC City',
                'code'         => 'HYD-HTEC',
                'address'      => '3rd Floor, CyberOne Building, Madhapur',
                'city'         => 'Hyderabad',
                'state'        => 'Telangana',
                'pincode'      => '500081',
                'phone'        => '+91-40-2345-9876',
                'email'        => 'hyd.branch@techflowsolutions.in',
                'manager_name' => 'Arjun Mehta',
                'is_head_office' => false,
                'is_active'    => true,
            ],
        ];

        foreach ($tenants as $i => $tenant) {
            // Give first tenant all branches, others get 1 branch each
            if ($i === 0) {
                foreach ($branchData as $data) {
                    Branch::firstOrCreate(
                        ['tenant_id' => $tenant->id, 'code' => $data['code']],
                        array_merge($data, ['tenant_id' => $tenant->id, 'country' => 'India'])
                    );
                }
                $this->command->info("Created 4 branches for tenant: {$tenant->name}");
            } else {
                Branch::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => 'HQ'],
                    [
                        'tenant_id'      => $tenant->id,
                        'name'           => $tenant->name . ' – Head Office',
                        'code'           => 'HQ',
                        'city'           => 'Mumbai',
                        'state'          => 'Maharashtra',
                        'country'        => 'India',
                        'is_head_office' => true,
                        'is_active'      => true,
                    ]
                );
                $this->command->info("Created 1 HQ branch for tenant: {$tenant->name}");
            }
        }
    }
}
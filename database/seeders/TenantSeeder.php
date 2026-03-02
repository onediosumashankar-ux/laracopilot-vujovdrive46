<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'name'       => 'TechFlow Solutions Pvt. Ltd.',
                'domain'     => 'techflow.hrms.local',
                'email'      => 'hr@techflowsolutions.in',
                'phone'      => '+91-80-4567-8900',
                'address'    => '4th Floor, Prestige Tech Park, Outer Ring Road, Bengaluru – 560103, Karnataka',
                'gstin'      => '29AADCT1234A1ZK',
                'pan'        => 'AADCT1234A',
                'industry'   => 'Information Technology',
                'is_active'  => true,
            ],
            [
                'name'       => 'Innovate Fintech India Pvt. Ltd.',
                'domain'     => 'innovatefin.hrms.local',
                'email'      => 'people@innovatefintech.in',
                'phone'      => '+91-22-6789-0123',
                'address'    => '12th Floor, One BKC, Bandra Kurla Complex, Mumbai – 400051, Maharashtra',
                'gstin'      => '27AABCI5678B1ZM',
                'pan'        => 'AABCI5678B',
                'industry'   => 'Financial Services',
                'is_active'  => true,
            ],
            [
                'name'       => 'HealthFirst Diagnostics Ltd.',
                'domain'     => 'healthfirst.hrms.local',
                'email'      => 'hr@healthfirstdiag.in',
                'phone'      => '+91-40-2345-6789',
                'address'    => 'Plot 15, Hitech City, Madhapur, Hyderabad – 500081, Telangana',
                'gstin'      => '36AADCH9876C1ZP',
                'pan'        => 'AADCH9876C',
                'industry'   => 'Healthcare',
                'is_active'  => true,
            ],
        ];

        foreach ($tenants as $data) {
            Tenant::firstOrCreate(['domain' => $data['domain']], $data);
        }

        $this->command->info('✅ 3 Indian tenants seeded.');
    }
}
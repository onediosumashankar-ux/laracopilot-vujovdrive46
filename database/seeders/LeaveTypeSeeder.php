<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\Tenant;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $leaveTypes = [
            ['name'=>'Earned Leave (EL)',         'code'=>'EL',   'days_per_year'=>15, 'paid'=>true,  'carry_forward'=>true,  'description'=>'Earned leave accrued at 1.25 days per month. Max carry-forward: 30 days.'],
            ['name'=>'Sick Leave (SL)',            'code'=>'SL',   'days_per_year'=>12, 'paid'=>true,  'carry_forward'=>false, 'description'=>'For medical illness with doctor certificate required for >2 consecutive days.'],
            ['name'=>'Casual Leave (CL)',          'code'=>'CL',   'days_per_year'=>8,  'paid'=>true,  'carry_forward'=>false, 'description'=>'For unforeseen personal needs. Max 3 consecutive days.'],
            ['name'=>'Maternity Leave (ML)',       'code'=>'ML',   'days_per_year'=>182,'paid'=>true,  'carry_forward'=>false, 'description'=>'26 weeks maternity leave as per Maternity Benefit Act 1961.'],
            ['name'=>'Paternity Leave (PL)',       'code'=>'PATL', 'days_per_year'=>5,  'paid'=>true,  'carry_forward'=>false, 'description'=>'5 days paid paternity leave within 3 months of childbirth.'],
            ['name'=>'Leave Without Pay (LWP)',    'code'=>'LWP',  'days_per_year'=>999,'paid'=>false, 'carry_forward'=>false, 'description'=>'Unpaid leave on exhaustion of all paid leaves. Salary deducted per working day.'],
            ['name'=>'Compensatory Off (CompOff)', 'code'=>'COMP', 'days_per_year'=>12, 'paid'=>true,  'carry_forward'=>false, 'description'=>'Comp off for working on weekends/holidays. Must be availed within 60 days.'],
            ['name'=>'Bereavement Leave',          'code'=>'BL',   'days_per_year'=>3,  'paid'=>true,  'carry_forward'=>false, 'description'=>'3 days paid leave on death of immediate family member.'],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $lt['code']],
                array_merge($lt, ['tenant_id' => $tenant->id])
            );
        }

        $this->command->info('✅ 8 Indian leave types seeded (EL/SL/CL/ML etc.).');
    }
}
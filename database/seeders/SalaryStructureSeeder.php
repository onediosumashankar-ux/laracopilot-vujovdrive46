<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\Tenant;

class SalaryStructureSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->command->warn('No tenant found. Run TenantSeeder first.');
            return;
        }
        $tenantId = $tenant->id;

        // ── 6 Real Indian Salary Structures ──────────────────────────────
        $structures = [
            [
                'name'        => 'Junior Software Engineer – Grade L1',
                'code'        => 'IT-L1',
                'description' => 'Entry level for fresh graduates and 0-2 years experience in IT/Software.',
                'type'        => 'annual',
                'ctc_amount'  => 400000.00,  // 4 LPA
                'is_active'   => true,
                'components'  => [
                    // Earnings
                    ['name'=>'Basic Salary',           'code'=>'BASIC',    'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>50,    'taxable'=>true,  'pf_applicable'=>true,  'esi_applicable'=>true,  'max_limit'=>null,      'sort_order'=>1],
                    ['name'=>'House Rent Allowance',   'code'=>'HRA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>40,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,      'sort_order'=>2],
                    ['name'=>'Transport Allowance',    'code'=>'TA',       'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>1600,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,      'sort_order'=>3],
                    ['name'=>'Special Allowance',      'code'=>'SPECIAL',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>8.33,  'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,      'sort_order'=>4],
                    ['name'=>'Medical Allowance',      'code'=>'MEDICAL',  'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>1250,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>15000,     'sort_order'=>5],
                    // Deductions
                    ['name'=>'Provident Fund (Emp)',   'code'=>'PF_EMP',   'type'=>'deduction',            'calculation_type'=>'percentage_basic',  'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,      'sort_order'=>10],
                    ['name'=>'ESIC (Employee)',        'code'=>'ESIC_EMP', 'type'=>'deduction',            'calculation_type'=>'percentage_gross',  'value'=>0.75,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,      'sort_order'=>11],
                    ['name'=>'Professional Tax',       'code'=>'PT',       'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,      'sort_order'=>12],
                    // Employer Contribution
                    ['name'=>'PF Employer Contrib.',   'code'=>'PF_ER',    'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,      'sort_order'=>20],
                    ['name'=>'ESIC (Employer)',        'code'=>'ESIC_ER',  'type'=>'employer_contribution', 'calculation_type'=>'percentage_gross', 'value'=>3.25,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,      'sort_order'=>21],
                ],
            ],
            [
                'name'        => 'Senior Software Engineer – Grade L3',
                'code'        => 'IT-L3',
                'description' => 'Mid-senior level for 4-8 years experienced software engineers.',
                'type'        => 'annual',
                'ctc_amount'  => 1200000.00, // 12 LPA
                'is_active'   => true,
                'components'  => [
                    ['name'=>'Basic Salary',           'code'=>'BASIC',    'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>40,    'taxable'=>true,  'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>1],
                    ['name'=>'House Rent Allowance',   'code'=>'HRA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>50,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>2],
                    ['name'=>'Transport Allowance',    'code'=>'TA',       'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>3200,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>3],
                    ['name'=>'Leave Travel Allowance', 'code'=>'LTA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>8.33,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>4],
                    ['name'=>'Special Allowance',      'code'=>'SPECIAL',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>8.67,  'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>5],
                    ['name'=>'Performance Bonus',      'code'=>'PERF_BON', 'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>8.33,  'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>6],
                    // Deductions
                    ['name'=>'Provident Fund (Emp)',   'code'=>'PF_EMP',   'type'=>'deduction',            'calculation_type'=>'percentage_basic',  'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>10],
                    ['name'=>'Professional Tax',       'code'=>'PT',       'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>11],
                    ['name'=>'Income Tax (TDS)',       'code'=>'TDS',      'type'=>'deduction',            'calculation_type'=>'percentage_ctc',   'value'=>5,     'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>12],
                    // Employer
                    ['name'=>'PF Employer Contrib.',   'code'=>'PF_ER',    'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>20],
                    ['name'=>'Gratuity',               'code'=>'GRATUITY', 'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>4.81,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>21],
                ],
            ],
            [
                'name'        => 'Engineering Manager – Grade M2',
                'code'        => 'IT-M2',
                'description' => 'Team lead/manager level for 8+ years with people management responsibilities.',
                'type'        => 'annual',
                'ctc_amount'  => 2500000.00, // 25 LPA
                'is_active'   => true,
                'components'  => [
                    ['name'=>'Basic Salary',           'code'=>'BASIC',    'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>40,    'taxable'=>true,  'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>1],
                    ['name'=>'House Rent Allowance',   'code'=>'HRA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>50,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>2],
                    ['name'=>'Leave Travel Allowance', 'code'=>'LTA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>8.33,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>3],
                    ['name'=>'Special Allowance',      'code'=>'SPECIAL',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>4,     'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>4],
                    ['name'=>'Performance Bonus',      'code'=>'PERF_BON', 'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>16.67, 'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>5],
                    ['name'=>'Vehicle Allowance',      'code'=>'VEHICLE',  'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>5000,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>6],
                    ['name'=>'Internet Allowance',     'code'=>'INTERNET', 'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>2000,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>7],
                    // Deductions
                    ['name'=>'Provident Fund (Emp)',   'code'=>'PF_EMP',   'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>1800,  'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>10],
                    ['name'=>'Professional Tax',       'code'=>'PT',       'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>11],
                    ['name'=>'Income Tax (TDS)',       'code'=>'TDS',      'type'=>'deduction',            'calculation_type'=>'percentage_ctc',   'value'=>15,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>12],
                    // Employer
                    ['name'=>'PF Employer Contrib.',   'code'=>'PF_ER',    'type'=>'employer_contribution', 'calculation_type'=>'fixed',            'value'=>1800,  'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>20],
                    ['name'=>'Gratuity',               'code'=>'GRATUITY', 'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>4.81,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>21],
                ],
            ],
            [
                'name'        => 'HR Executive – Grade H1',
                'code'        => 'HR-H1',
                'description' => 'HR executive for 1-4 years experience in human resources functions.',
                'type'        => 'annual',
                'ctc_amount'  => 600000.00,  // 6 LPA
                'is_active'   => true,
                'components'  => [
                    ['name'=>'Basic Salary',           'code'=>'BASIC',    'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>50,    'taxable'=>true,  'pf_applicable'=>true,  'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>1],
                    ['name'=>'House Rent Allowance',   'code'=>'HRA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>40,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>2],
                    ['name'=>'Transport Allowance',    'code'=>'TA',       'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>1600,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>3],
                    ['name'=>'Special Allowance',      'code'=>'SPECIAL',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>4.33,  'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>4],
                    ['name'=>'Children Education',     'code'=>'CEA',      'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>2400,  'sort_order'=>5],
                    // Deductions
                    ['name'=>'Provident Fund (Emp)',   'code'=>'PF_EMP',   'type'=>'deduction',            'calculation_type'=>'percentage_basic',  'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>10],
                    ['name'=>'ESIC (Employee)',        'code'=>'ESIC_EMP', 'type'=>'deduction',            'calculation_type'=>'percentage_gross',  'value'=>0.75,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>11],
                    ['name'=>'Professional Tax',       'code'=>'PT',       'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>12],
                    // Employer
                    ['name'=>'PF Employer Contrib.',   'code'=>'PF_ER',    'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>20],
                    ['name'=>'ESIC (Employer)',        'code'=>'ESIC_ER',  'type'=>'employer_contribution', 'calculation_type'=>'percentage_gross', 'value'=>3.25,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>21],
                    ['name'=>'Gratuity',               'code'=>'GRATUITY', 'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>4.81,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>22],
                ],
            ],
            [
                'name'        => 'Finance Analyst – Grade F2',
                'code'        => 'FIN-F2',
                'description' => 'Finance and accounts for CA/MBA Finance with 2-6 years experience.',
                'type'        => 'annual',
                'ctc_amount'  => 900000.00,  // 9 LPA
                'is_active'   => true,
                'components'  => [
                    ['name'=>'Basic Salary',           'code'=>'BASIC',    'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>40,    'taxable'=>true,  'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>1],
                    ['name'=>'House Rent Allowance',   'code'=>'HRA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>50,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>2],
                    ['name'=>'Transport Allowance',    'code'=>'TA',       'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>3200,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>3],
                    ['name'=>'Leave Travel Allowance', 'code'=>'LTA',      'type'=>'earning',              'calculation_type'=>'percentage_basic',  'value'=>8.33,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>4],
                    ['name'=>'Special Allowance',      'code'=>'SPECIAL',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>6.27,  'taxable'=>true,  'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>5],
                    // Deductions
                    ['name'=>'Provident Fund (Emp)',   'code'=>'PF_EMP',   'type'=>'deduction',            'calculation_type'=>'percentage_basic',  'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>10],
                    ['name'=>'Professional Tax',       'code'=>'PT',       'type'=>'deduction',            'calculation_type'=>'fixed',             'value'=>200,   'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>11],
                    ['name'=>'Income Tax (TDS)',       'code'=>'TDS',      'type'=>'deduction',            'calculation_type'=>'percentage_ctc',   'value'=>3,     'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>12],
                    // Employer
                    ['name'=>'PF Employer Contrib.',   'code'=>'PF_ER',    'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>12,    'taxable'=>false, 'pf_applicable'=>true,  'esi_applicable'=>false, 'max_limit'=>1800,  'sort_order'=>20],
                    ['name'=>'Gratuity',               'code'=>'GRATUITY', 'type'=>'employer_contribution', 'calculation_type'=>'percentage_basic', 'value'=>4.81,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>21],
                ],
            ],
            [
                'name'        => 'Operations Intern – Grade I1',
                'code'        => 'OPS-I1',
                'description' => 'Intern / trainee stipend structure for freshers and industrial trainees.',
                'type'        => 'monthly',
                'ctc_amount'  => 20000.00,   // 20K/month stipend
                'is_active'   => true,
                'components'  => [
                    ['name'=>'Stipend',                'code'=>'STIPEND',  'type'=>'earning',              'calculation_type'=>'percentage_ctc',   'value'=>80,    'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>1],
                    ['name'=>'Meal Allowance',         'code'=>'MEAL',     'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>1500,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>2],
                    ['name'=>'Transport Allowance',    'code'=>'TA',       'type'=>'earning',              'calculation_type'=>'fixed',             'value'=>2500,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>false, 'max_limit'=>null,  'sort_order'=>3],
                    // Deductions
                    ['name'=>'ESIC (Employee)',        'code'=>'ESIC_EMP', 'type'=>'deduction',            'calculation_type'=>'percentage_gross',  'value'=>0.75,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>10],
                    // Employer
                    ['name'=>'ESIC (Employer)',        'code'=>'ESIC_ER',  'type'=>'employer_contribution', 'calculation_type'=>'percentage_gross', 'value'=>3.25,  'taxable'=>false, 'pf_applicable'=>false, 'esi_applicable'=>true,  'max_limit'=>null,  'sort_order'=>20],
                ],
            ],
        ];

        foreach ($structures as $structureData) {
            $components = $structureData['components'];
            unset($structureData['components']);

            $structure = SalaryStructure::create(array_merge($structureData, ['tenant_id' => $tenantId]));

            foreach ($components as $comp) {
                SalaryComponent::create(array_merge($comp, [
                    'salary_structure_id' => $structure->id,
                    'is_active'           => true,
                    'formula'             => null,
                ]));
            }

            $this->command->info("Created structure: {$structure->name} ({$structure->code})");
        }
    }
}
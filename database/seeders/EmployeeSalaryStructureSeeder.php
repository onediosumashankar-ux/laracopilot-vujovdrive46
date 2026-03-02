<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\EmployeeSalaryStructure;
use App\Models\SalaryBreakdown;
use App\Services\SalaryStructureService;

class EmployeeSalaryStructureSeeder extends Seeder
{
    public function run(): void
    {
        $employees  = Employee::all();
        $service    = new SalaryStructureService();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Run EmployeeSeeder first.');
            return;
        }

        // Map departments/positions to appropriate structures
        $structureMap = [
            'IT-L1'   => SalaryStructure::where('code', 'IT-L1')->first(),
            'IT-L3'   => SalaryStructure::where('code', 'IT-L3')->first(),
            'IT-M2'   => SalaryStructure::where('code', 'IT-M2')->first(),
            'HR-H1'   => SalaryStructure::where('code', 'HR-H1')->first(),
            'FIN-F2'  => SalaryStructure::where('code', 'FIN-F2')->first(),
            'OPS-I1'  => SalaryStructure::where('code', 'OPS-I1')->first(),
        ];

        // Real Indian employee sample data with CTC overrides
        $assignments = [
            [
                'name_match' => null, // first available employee
                'structure'  => 'IT-L3',
                'ctc'        => 1350000, // 13.5 LPA
                'from'       => '2023-04-01',
                'notes'      => 'Annual increment – FY 2023-24. Performance rating: Exceeds Expectations.',
                'department' => 'Engineering',
                'position'   => 'Senior Software Engineer',
            ],
            [
                'name_match' => null,
                'structure'  => 'IT-L1',
                'ctc'        => 420000, // 4.2 LPA
                'from'       => '2024-01-15',
                'notes'      => 'New joinee offer – Lateral hire from TCS.',
                'department' => 'Engineering',
                'position'   => 'Software Engineer',
            ],
            [
                'name_match' => null,
                'structure'  => 'IT-M2',
                'ctc'        => 2800000, // 28 LPA
                'from'       => '2022-07-01',
                'notes'      => 'Promoted to EM – Grade M2. Includes performance bonus component.',
                'department' => 'Engineering',
                'position'   => 'Engineering Manager',
            ],
            [
                'name_match' => null,
                'structure'  => 'HR-H1',
                'ctc'        => 650000, // 6.5 LPA
                'from'       => '2023-06-01',
                'notes'      => 'HR Executive – Recruitment & Onboarding specialist.',
                'department' => 'Human Resources',
                'position'   => 'HR Executive',
            ],
            [
                'name_match' => null,
                'structure'  => 'FIN-F2',
                'ctc'        => 950000, // 9.5 LPA
                'from'       => '2023-04-01',
                'notes'      => 'Finance Analyst – Accounts payable & MIS reporting.',
                'department' => 'Finance',
                'position'   => 'Finance Analyst',
            ],
            [
                'name_match' => null,
                'structure'  => 'OPS-I1',
                'ctc'        => 20000, // 20K/month
                'from'       => '2024-06-01',
                'notes'      => 'Summer intern – 6-month industrial training program.',
                'department' => 'Operations',
                'position'   => 'Operations Intern',
            ],
        ];

        $employeeList = $employees->values();
        $count        = 0;

        foreach ($assignments as $index => $assignment) {
            $structure = $structureMap[$assignment['structure']] ?? null;
            $employee  = $employeeList->get($index);

            if (!$structure || !$employee) {
                $this->command->warn("Skipping index $index – structure or employee not found.");
                continue;
            }

            // Deactivate any existing current assignment
            EmployeeSalaryStructure::where('employee_id', $employee->id)
                ->where('is_current', true)
                ->update(['is_current' => false, 'effective_to' => now()->toDateString()]);

            $ctc = (float)$assignment['ctc'];

            $ess = EmployeeSalaryStructure::create([
                'tenant_id'           => $employee->tenant_id,
                'employee_id'         => $employee->id,
                'salary_structure_id' => $structure->id,
                'ctc_override'        => $ctc,
                'effective_from'      => $assignment['from'],
                'effective_to'        => null,
                'is_current'          => true,
                'notes'               => $assignment['notes'],
                'created_by'          => 1,
            ]);

            // Update employee salary
            $annualCtc = $structure->type === 'annual' ? $ctc : $ctc * 12;
            $employee->update(['salary' => $annualCtc]);

            // Compute & save breakdown
            $computation = $service->compute($structure, $ctc);
            $service->saveBreakdown($ess, $computation);

            $count++;
            $this->command->info(
                "Assigned: {$employee->first_name} {$employee->last_name} "
                . "→ {$structure->name} | CTC: ₹" . number_format($ctc, 0)
                . " | Net/mo: ₹" . number_format($computation['net_monthly'], 0)
            );
        }

        $this->command->info("\n✅ {$count} employee salary structures assigned with full breakdowns.");
    }
}
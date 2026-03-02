<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfferLetter;
use App\Models\Employee;
use App\Models\Candidate;
use App\Models\SalaryStructure;
use App\Models\EmployeeSalaryStructure;
use App\Models\Tenant;

class OfferLetterSeeder extends Seeder
{
    public function run(): void
    {
        $tenant     = Tenant::first();
        if (!$tenant) return;

        $tenantId   = $tenant->id;
        $employees  = Employee::where('tenant_id', $tenantId)->get();
        $candidates = Candidate::where('tenant_id', $tenantId)->get();

        $structures = [
            'IT-L1'  => SalaryStructure::where('code', 'IT-L1')->first(),
            'IT-L3'  => SalaryStructure::where('code', 'IT-L3')->first(),
            'IT-M2'  => SalaryStructure::where('code', 'IT-M2')->first(),
            'HR-H1'  => SalaryStructure::where('code', 'HR-H1')->first(),
            'FIN-F2' => SalaryStructure::where('code', 'FIN-F2')->first(),
        ];

        $offerData = [
            // Accepted offers for employees (salary revision / promotion)
            [
                'recipient_type'      => 'employee',
                'recipient_index'     => 0,
                'structure_code'      => 'IT-M2',
                'position'            => 'Engineering Manager',
                'department'          => 'Engineering',
                'ctc_annual'          => 2800000,
                'employment_type'     => 'full_time',
                'joining_date'        => '2022-07-01',
                'offer_expiry'        => '2022-06-20',
                'work_location'       => 'Bengaluru – Koramangala Office',
                'status'              => 'accepted',
                'sent_at'             => '2022-06-10 10:00:00',
                'responded_at'        => '2022-06-15 11:30:00',
                'custom_clauses'      => "Probation Period: 6 months from date of joining.\nNotice Period: 90 days post-confirmation.\nAnnual performance appraisal in April every year.\nStock Options (ESOPs): 1000 units vesting over 4 years with 1-year cliff.\nTraining Bond: Nil.",
            ],
            [
                'recipient_type'      => 'employee',
                'recipient_index'     => 1,
                'structure_code'      => 'IT-L1',
                'position'            => 'Junior Software Engineer',
                'department'          => 'Engineering',
                'ctc_annual'          => 420000,
                'employment_type'     => 'full_time',
                'joining_date'        => '2024-01-15',
                'offer_expiry'        => '2024-01-10',
                'work_location'       => 'Pune – Hinjewadi IT Park',
                'status'              => 'accepted',
                'sent_at'             => '2023-12-28 09:00:00',
                'responded_at'        => '2024-01-02 14:00:00',
                'custom_clauses'      => "Probation Period: 3 months.\nNotice Period: 30 days during probation, 60 days post-confirmation.\nTraining Bond: 1 year bond for technical certifications sponsored by company.\nWork from Office: 5 days/week.",
            ],
            [
                'recipient_type'      => 'employee',
                'recipient_index'     => 3,
                'structure_code'      => 'HR-H1',
                'position'            => 'HR Executive',
                'department'          => 'Human Resources',
                'ctc_annual'          => 650000,
                'employment_type'     => 'full_time',
                'joining_date'        => '2023-06-01',
                'offer_expiry'        => '2023-05-25',
                'work_location'       => 'Mumbai – Andheri East',
                'status'              => 'accepted',
                'sent_at'             => '2023-05-18 11:00:00',
                'responded_at'        => '2023-05-22 16:00:00',
                'custom_clauses'      => "Probation Period: 3 months.\nNotice Period: 30 days.\nWeekly Off: Saturday & Sunday.\nFlexible work timings: 9:30 AM – 6:30 PM IST.",
            ],
            // Pending offers for candidates
            [
                'recipient_type'      => 'candidate',
                'recipient_index'     => 0,
                'structure_code'      => 'IT-L3',
                'position'            => 'Senior Software Engineer – Full Stack',
                'department'          => 'Product Engineering',
                'ctc_annual'          => 1500000,
                'employment_type'     => 'full_time',
                'joining_date'        => now()->addDays(30)->format('Y-m-d'),
                'offer_expiry'        => now()->addDays(10)->format('Y-m-d'),
                'work_location'       => 'Hyderabad – HITEC City (Hybrid)',
                'status'              => 'sent',
                'sent_at'             => now()->subDays(3)->format('Y-m-d') . ' 10:00:00',
                'responded_at'        => null,
                'custom_clauses'      => "Probation Period: 6 months.\nNotice Period: 60 days post-confirmation.\nWork Mode: Hybrid – 3 days office / 2 days remote.\nEquipment: Laptop + peripherals provided by company.\nAnnual Leave: 24 days paid leave per calendar year.",
            ],
            [
                'recipient_type'      => 'candidate',
                'recipient_index'     => 1,
                'structure_code'      => 'FIN-F2',
                'position'            => 'Senior Finance Analyst',
                'department'          => 'Finance & Accounts',
                'ctc_annual'          => 1050000,
                'employment_type'     => 'full_time',
                'joining_date'        => now()->addDays(45)->format('Y-m-d'),
                'offer_expiry'        => now()->addDays(7)->format('Y-m-d'),
                'work_location'       => 'Chennai – Guindy',
                'status'              => 'draft',
                'sent_at'             => null,
                'responded_at'        => null,
                'custom_clauses'      => "Probation Period: 3 months.\nNotice Period: 45 days.\nAnnual audit bonus applicable on performance.\nWork from Office: 5 days/week.",
            ],
            // Rejected offer (for realistic data)
            [
                'recipient_type'      => 'candidate',
                'recipient_index'     => 2,
                'structure_code'      => 'IT-L1',
                'position'            => 'Software Engineer – Backend',
                'department'          => 'Engineering',
                'ctc_annual'          => 480000,
                'employment_type'     => 'full_time',
                'joining_date'        => now()->subDays(60)->format('Y-m-d'),
                'offer_expiry'        => now()->subDays(50)->format('Y-m-d'),
                'work_location'       => 'Delhi NCR – Gurugram',
                'status'              => 'rejected',
                'sent_at'             => now()->subDays(65)->format('Y-m-d') . ' 10:00:00',
                'responded_at'        => now()->subDays(58)->format('Y-m-d') . ' 14:00:00',
                'custom_clauses'      => "Probation Period: 3 months.\nNotice Period: 30 days.",
            ],
            // Expired offer
            [
                'recipient_type'      => 'candidate',
                'recipient_index'     => 3,
                'structure_code'      => 'HR-H1',
                'position'            => 'HR Executive – Talent Acquisition',
                'department'          => 'Human Resources',
                'ctc_annual'          => 600000,
                'employment_type'     => 'full_time',
                'joining_date'        => now()->subDays(45)->format('Y-m-d'),
                'offer_expiry'        => now()->subDays(30)->format('Y-m-d'),
                'work_location'       => 'Bengaluru – HSR Layout',
                'status'              => 'expired',
                'sent_at'             => now()->subDays(50)->format('Y-m-d') . ' 10:00:00',
                'responded_at'        => null,
                'custom_clauses'      => "Probation Period: 3 months.\nNotice Period: 30 days.\nTarget-based incentives applicable.",
            ],
        ];

        $offerCount = 0;
        foreach ($offerData as $data) {
            $structure = $structures[$data['structure_code']] ?? null;
            if (!$structure) {
                $this->command->warn("Structure {$data['structure_code']} not found, skipping.");
                continue;
            }

            $employeeId  = null;
            $candidateId = null;
            $essId       = null;

            if ($data['recipient_type'] === 'employee') {
                $emp = $employees->get($data['recipient_index']);
                if (!$emp) continue;
                $employeeId = $emp->id;
                $ess = EmployeeSalaryStructure::where('employee_id', $emp->id)
                    ->where('is_current', true)->first();
                $essId = $ess?->id;
            } else {
                $cand = $candidates->get($data['recipient_index']);
                if (!$cand) {
                    // Create a minimal candidate reference if not enough candidates
                    $this->command->warn("Candidate index {$data['recipient_index']} not found, skipping.");
                    continue;
                }
                $candidateId = $cand->id;
            }

            $offerNumber = 'OFR-' . strtoupper(substr($tenant->name ?? 'TFL', 0, 3))
                . '-' . date('Y')
                . '-' . str_pad($offerCount + 1, 3, '0', STR_PAD_LEFT);

            OfferLetter::create([
                'tenant_id'                   => $tenantId,
                'employee_id'                 => $employeeId,
                'candidate_id'                => $candidateId,
                'employee_salary_structure_id'=> $essId,
                'offer_number'                => $offerNumber,
                'position'                    => $data['position'],
                'department'                  => $data['department'],
                'joining_date'                => $data['joining_date'],
                'offer_expiry'                => $data['offer_expiry'],
                'ctc_annual'                  => $data['ctc_annual'],
                'employment_type'             => $data['employment_type'],
                'work_location'               => $data['work_location'],
                'custom_clauses'              => $data['custom_clauses'],
                'status'                      => $data['status'],
                'sent_at'                     => $data['sent_at'],
                'responded_at'                => $data['responded_at'],
                'created_by'                  => 1,
            ]);

            $offerCount++;
            $recipient = $data['recipient_type'] === 'employee'
                ? ($employees->get($data['recipient_index'])->first_name ?? 'Employee')
                : ($candidates->get($data['recipient_index'])->full_name ?? 'Candidate');
            $this->command->info(
                "Offer #{$offerNumber} | {$data['position']} | ₹"
                . number_format($data['ctc_annual'], 0)
                . " | Status: {$data['status']} → {$recipient}"
            );
        }

        $this->command->info("\n✅ {$offerCount} offer letters seeded.");
    }
}
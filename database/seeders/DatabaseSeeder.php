<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed order matters – dependencies first.
     */
    public function run(): void
    {
        $this->call([
            // 1. Foundation
            TenantSeeder::class,
            UserSeeder::class,

            // 2. HR Core
            EmployeeSeeder::class,
            LeaveTypeSeeder::class,
            HolidaySeeder::class,
            ShiftSeeder::class,

            // 3. Recruitment
            JobPostingSeeder::class,
            CandidateSeeder::class,

            // 4. Compensation – ORDER IS CRITICAL
            SalaryStructureSeeder::class,         // creates templates + components
            EmployeeSalaryStructureSeeder::class, // assigns + computes breakdowns
            OfferLetterSeeder::class,             // generates offer letters

            // 5. Operations
            AttendanceSeeder::class,
            LeaveRequestSeeder::class,
            PayrollSeeder::class,

            // 6. Development
            TrainingProgramSeeder::class,
            WellnessSurveySeeder::class,
            PerformanceReviewSeeder::class,
            BenefitSeeder::class,
            OnboardingSeeder::class,
        ]);
    }
}
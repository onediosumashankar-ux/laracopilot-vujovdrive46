<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Employee;
use App\Models\JobPosting;
use App\Models\Candidate;
use App\Models\Attendance;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PerformanceReview;
use App\Models\Benefit;
use App\Models\Shift;
use App\Models\TrainingProgram;
use App\Models\WellnessSurvey;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@hrms.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
            'tenant_id' => null,
        ]);

        // Tenant 1 - TechCorp Solutions
        $tenant1 = Tenant::create([
            'name' => 'TechCorp Solutions',
            'domain' => 'techcorp',
            'email' => 'admin@techcorp.com',
            'phone' => '+1-555-0100',
            'address' => '100 Tech Boulevard, San Francisco, CA 94105',
            'plan' => 'enterprise',
            'status' => 'active',
            'office_lat' => 37.7749,
            'office_lng' => -122.4194,
        ]);

        // Tenant 1 Admin
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'admin@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $tenant1->id,
        ]);

        // Tenant 1 HR Manager
        User::create([
            'name' => 'Mike Chen',
            'email' => 'hr@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'tenant_id' => $tenant1->id,
        ]);

        // Tenant 2 - Global Finance Inc
        $tenant2 = Tenant::create([
            'name' => 'Global Finance Inc',
            'domain' => 'globalfinance',
            'email' => 'admin@globalfinance.com',
            'phone' => '+1-555-0200',
            'address' => '200 Wall Street, New York, NY 10005',
            'plan' => 'professional',
            'status' => 'active',
            'office_lat' => 40.7074,
            'office_lng' => -74.0113,
        ]);

        User::create([
            'name' => 'David Williams',
            'email' => 'admin@globalfinance.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $tenant2->id,
        ]);

        // Tenant 3
        $tenant3 = Tenant::create([
            'name' => 'HealthFirst Medical',
            'domain' => 'healthfirst',
            'email' => 'admin@healthfirst.com',
            'phone' => '+1-555-0300',
            'address' => '300 Medical Drive, Chicago, IL 60601',
            'plan' => 'starter',
            'status' => 'active',
            'office_lat' => 41.8781,
            'office_lng' => -87.6298,
        ]);

        User::create([
            'name' => 'Emily Davis',
            'email' => 'admin@healthfirst.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $tenant3->id,
        ]);

        // TechCorp Employees
        $departments = ['Engineering', 'Product', 'Sales', 'Marketing', 'HR', 'Finance', 'Operations', 'Design'];
        $positions = ['Senior Developer', 'Product Manager', 'Sales Executive', 'Marketing Lead', 'HR Specialist', 'Financial Analyst', 'Operations Manager', 'UI/UX Designer'];

        $employees = [];
        $employeeData = [
            ['James', 'Wilson', 'james.wilson@techcorp.com', 'Engineering', 'Senior Developer', 95000],
            ['Anna', 'Martinez', 'anna.martinez@techcorp.com', 'Product', 'Product Manager', 88000],
            ['Robert', 'Brown', 'robert.brown@techcorp.com', 'Sales', 'Sales Executive', 72000],
            ['Lisa', 'Taylor', 'lisa.taylor@techcorp.com', 'Marketing', 'Marketing Lead', 78000],
            ['Kevin', 'Anderson', 'kevin.anderson@techcorp.com', 'Engineering', 'Backend Developer', 90000],
            ['Jessica', 'Thomas', 'jessica.thomas@techcorp.com', 'Design', 'UI/UX Designer', 82000],
            ['Michael', 'Jackson', 'michael.jackson@techcorp.com', 'Finance', 'Financial Analyst', 85000],
            ['Rachel', 'White', 'rachel.white@techcorp.com', 'HR', 'HR Specialist', 68000],
            ['Daniel', 'Harris', 'daniel.harris@techcorp.com', 'Engineering', 'DevOps Engineer', 92000],
            ['Sophie', 'Clark', 'sophie.clark@techcorp.com', 'Operations', 'Operations Manager', 80000],
            ['Chris', 'Lewis', 'chris.lewis@techcorp.com', 'Engineering', 'Frontend Developer', 88000],
            ['Amanda', 'Robinson', 'amanda.robinson@techcorp.com', 'Sales', 'Account Executive', 70000],
            ['Nathan', 'Walker', 'nathan.walker@techcorp.com', 'Product', 'Business Analyst', 75000],
            ['Olivia', 'Hall', 'olivia.hall@techcorp.com', 'Marketing', 'Content Strategist', 65000],
            ['Tyler', 'Young', 'tyler.young@techcorp.com', 'Engineering', 'QA Engineer', 78000],
        ];

        foreach ($employeeData as $index => $data) {
            $emp = Employee::create([
                'tenant_id' => $tenant1->id,
                'first_name' => $data[0],
                'last_name' => $data[1],
                'email' => $data[2],
                'phone' => '+1-555-' . str_pad($index + 1001, 4, '0', STR_PAD_LEFT),
                'department' => $data[3],
                'position' => $data[4],
                'employment_type' => 'full_time',
                'hire_date' => now()->subDays(rand(30, 1000))->format('Y-m-d'),
                'salary' => $data[5],
                'status' => 'active',
                'gender' => $index % 2 == 0 ? 'male' : 'female',
                'date_of_birth' => now()->subYears(rand(25, 45))->format('Y-m-d'),
                'address' => rand(100, 999) . ' Main St, San Francisco, CA',
                'emergency_contact' => 'Emergency Contact: +1-555-' . str_pad($index + 2001, 4, '0', STR_PAD_LEFT),
                'bank_account' => 'XXXX-XXXX-XXXX-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'tax_id' => 'TAX-' . str_pad($index + 10000, 5, '0', STR_PAD_LEFT),
            ]);

            User::create([
                'name' => $data[0] . ' ' . $data[1],
                'email' => $data[2],
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'tenant_id' => $tenant1->id,
                'employee_id' => $emp->id,
            ]);

            $employees[] = $emp;
        }

        // Leave Types
        $leaveTypes = [
            ['name' => 'Annual Leave', 'days_allowed' => 21, 'carry_forward' => true, 'paid' => true],
            ['name' => 'Sick Leave', 'days_allowed' => 14, 'carry_forward' => false, 'paid' => true],
            ['name' => 'Maternity Leave', 'days_allowed' => 90, 'carry_forward' => false, 'paid' => true],
            ['name' => 'Paternity Leave', 'days_allowed' => 14, 'carry_forward' => false, 'paid' => true],
            ['name' => 'Emergency Leave', 'days_allowed' => 5, 'carry_forward' => false, 'paid' => false],
            ['name' => 'Study Leave', 'days_allowed' => 10, 'carry_forward' => false, 'paid' => false],
        ];

        foreach ($leaveTypes as $lt) {
            \App\Models\LeaveType::create(array_merge($lt, ['tenant_id' => $tenant1->id]));
        }

        // Job Postings
        $jobs = [
            ['Senior React Developer', 'Engineering', 'San Francisco, CA', 'full_time', 'We are looking for an experienced React developer...', '5+ years React experience, TypeScript, Redux', 100000, 130000, 2],
            ['Product Designer', 'Design', 'Remote', 'remote', 'Join our world-class design team...', 'Figma proficiency, 3+ years UX experience', 80000, 100000, 1],
            ['Sales Manager', 'Sales', 'New York, NY', 'full_time', 'Lead our growing sales team...', '7+ years sales experience, SaaS background', 90000, 120000, 1],
            ['DevOps Engineer', 'Engineering', 'San Francisco, CA', 'full_time', 'Build and maintain our cloud infrastructure...', 'AWS, Kubernetes, CI/CD experience', 110000, 140000, 2],
            ['HR Business Partner', 'HR', 'Hybrid', 'full_time', 'Support our HR initiatives...', 'SHRM certification, 5+ years HR experience', 75000, 95000, 1],
        ];

        foreach ($jobs as $job) {
            $jp = JobPosting::create([
                'tenant_id' => $tenant1->id,
                'title' => $job[0],
                'department' => $job[1],
                'location' => $job[2],
                'type' => $job[3],
                'description' => $job[4],
                'requirements' => $job[5],
                'salary_min' => $job[6],
                'salary_max' => $job[7],
                'deadline' => now()->addDays(rand(30, 60))->format('Y-m-d'),
                'status' => 'open',
                'vacancies' => $job[8],
            ]);

            // Candidates for each job
            $candidateNames = [['Alex', 'Peterson'], ['Maria', 'Garcia'], ['John', 'Smith'], ['Emma', 'Johnson']];
            $statuses = ['applied', 'screening', 'interview', 'shortlisted'];
            foreach ($candidateNames as $ci => $cname) {
                Candidate::create([
                    'tenant_id' => $tenant1->id,
                    'job_posting_id' => $jp->id,
                    'first_name' => $cname[0],
                    'last_name' => $cname[1],
                    'email' => strtolower($cname[0]) . '.' . strtolower($cname[1]) . $jp->id . '@email.com',
                    'phone' => '+1-555-9' . str_pad($ci + 1, 3, '0', STR_PAD_LEFT),
                    'status' => $statuses[$ci],
                    'experience_years' => rand(3, 12),
                    'expected_salary' => rand(80000, 120000),
                    'source' => ['LinkedIn', 'Indeed', 'Referral', 'Website'][array_rand(['LinkedIn', 'Indeed', 'Referral', 'Website'])],
                ]);
            }
        }

        // Attendance Records
        foreach (array_slice($employees, 0, 10) as $emp) {
            for ($i = 0; $i < 20; $i++) {
                $day = now()->subDays($i);
                if ($day->isWeekend()) continue;
                $checkIn = $day->copy()->setHour(rand(8, 9))->setMinute(rand(0, 59));
                $checkOut = $day->copy()->setHour(rand(17, 19))->setMinute(rand(0, 59));
                Attendance::create([
                    'tenant_id' => $tenant1->id,
                    'employee_id' => $emp->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'check_in_lat' => 37.7749 + (rand(-10, 10) / 10000),
                    'check_in_lng' => -122.4194 + (rand(-10, 10) / 10000),
                    'hours_worked' => round($checkIn->diffInMinutes($checkOut) / 60, 2),
                    'is_late' => $checkIn->format('H:i') > '09:15',
                    'status' => 'present',
                    'approved' => rand(0, 1),
                ]);
            }
        }

        // Payrolls
        foreach ($employees as $emp) {
            Payroll::create([
                'tenant_id' => $tenant1->id,
                'employee_id' => $emp->id,
                'pay_period_start' => now()->startOfMonth()->format('Y-m-d'),
                'pay_period_end' => now()->endOfMonth()->format('Y-m-d'),
                'basic_salary' => $emp->salary / 12,
                'allowances' => rand(200, 800),
                'deductions' => rand(50, 200),
                'tax' => ($emp->salary / 12) * 0.22,
                'bonus' => rand(0, 500),
                'overtime_pay' => rand(0, 300),
                'gross_salary' => ($emp->salary / 12) + rand(200, 1000),
                'net_salary' => ($emp->salary / 12) * 0.78,
                'status' => ['pending', 'paid'][rand(0, 1)],
            ]);
        }

        // Performance Reviews
        foreach (array_slice($employees, 0, 8) as $emp) {
            PerformanceReview::create([
                'tenant_id' => $tenant1->id,
                'employee_id' => $emp->id,
                'review_period' => 'Q4 2024',
                'review_date' => now()->subDays(rand(10, 60))->format('Y-m-d'),
                'overall_rating' => round(rand(30, 50) / 10, 1),
                'quality_of_work' => round(rand(30, 50) / 10, 1),
                'productivity' => round(rand(30, 50) / 10, 1),
                'teamwork' => round(rand(30, 50) / 10, 1),
                'communication' => round(rand(30, 50) / 10, 1),
                'attendance_rating' => round(rand(30, 50) / 10, 1),
                'goals_achieved' => 'Completed all Q4 project milestones, exceeded sales targets by 15%',
                'strengths' => 'Excellent problem-solving skills, strong team collaboration, proactive communication',
                'areas_for_improvement' => 'Time management during peak periods, documentation practices',
                'feedback' => 'Outstanding performance this quarter. Continue the great work and focus on mentoring junior team members.',
                'status' => ['draft', 'completed', 'acknowledged'][rand(0, 2)],
            ]);
        }

        // Benefits
        $benefitsList = [
            ['Medical Health Insurance', 'health_insurance', 'Comprehensive health coverage including hospitalization', 350, 'monthly'],
            ['Dental Coverage', 'dental', 'Annual dental care including cleanings and procedures', 75, 'monthly'],
            ['Vision Benefits', 'vision', 'Annual eye exam and corrective lenses allowance', 50, 'monthly'],
            ['Life Insurance', 'life_insurance', '2x annual salary life insurance coverage', 100, 'monthly'],
            ['401k Retirement Match', 'retirement', 'Company matches up to 5% of salary contribution', 0, 'monthly'],
            ['Transport Allowance', 'transport', 'Monthly commuter pass or parking reimbursement', 150, 'monthly'],
            ['Meal Stipend', 'meal', 'Daily lunch allowance for office days', 300, 'monthly'],
            ['Education Reimbursement', 'education', 'Annual tuition and professional development reimbursement', 2000, 'annually'],
        ];

        foreach ($benefitsList as $benefit) {
            Benefit::create([
                'tenant_id' => $tenant1->id,
                'name' => $benefit[0],
                'type' => $benefit[1],
                'description' => $benefit[2],
                'amount' => $benefit[3],
                'frequency' => $benefit[4],
                'eligibility' => 'All full-time employees after 90 days probation',
                'status' => 'active',
            ]);
        }

        // Shifts
        Shift::create(['tenant_id' => $tenant1->id, 'name' => 'Morning Shift', 'start_time' => '07:00', 'end_time' => '15:00', 'grace_period' => 10]);
        Shift::create(['tenant_id' => $tenant1->id, 'name' => 'Day Shift', 'start_time' => '09:00', 'end_time' => '17:00', 'grace_period' => 15]);
        Shift::create(['tenant_id' => $tenant1->id, 'name' => 'Evening Shift', 'start_time' => '13:00', 'end_time' => '21:00', 'grace_period' => 15]);
        Shift::create(['tenant_id' => $tenant1->id, 'name' => 'Night Shift', 'start_time' => '21:00', 'end_time' => '05:00', 'grace_period' => 10]);

        // Training Programs
        $programs = [
            ['Leadership Excellence Program', 'Develop leadership skills for mid-level managers', 'leadership', 'blended', 40, 'Dr. Sarah Lee'],
            ['Advanced React & TypeScript', 'Deep dive into modern React development patterns', 'technical', 'online', 24, 'John Dev'],
            ['Data Privacy & GDPR Compliance', 'Essential compliance training for all staff', 'compliance', 'self_paced', 8, 'Legal Team'],
            ['Effective Communication Skills', 'Enhance workplace communication and presentation skills', 'soft_skills', 'classroom', 16, 'Prof. Maria'],
            ['Cloud Architecture on AWS', 'Design and deploy scalable cloud solutions', 'technical', 'online', 32, 'AWS Expert'],
        ];

        foreach ($programs as $prog) {
            TrainingProgram::create([
                'tenant_id' => $tenant1->id,
                'title' => $prog[0],
                'description' => $prog[1],
                'category' => $prog[2],
                'delivery_mode' => $prog[3],
                'duration_hours' => $prog[4],
                'start_date' => now()->addDays(rand(7, 30))->format('Y-m-d'),
                'end_date' => now()->addDays(rand(45, 90))->format('Y-m-d'),
                'instructor' => $prog[5],
                'max_participants' => rand(15, 30),
                'status' => 'active',
            ]);
        }

        // Wellness Surveys
        WellnessSurvey::create([
            'tenant_id' => $tenant1->id,
            'title' => 'Q1 2025 Employee Engagement Survey',
            'description' => 'Help us improve your workplace experience by sharing your feedback.',
            'questions' => json_encode([
                ['q' => 'How satisfied are you with your current role?', 'type' => 'scale'],
                ['q' => 'Do you feel recognized for your contributions?', 'type' => 'scale'],
                ['q' => 'How would you rate work-life balance?', 'type' => 'scale'],
                ['q' => 'What can we do better?', 'type' => 'text'],
            ]),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'anonymous' => true,
            'status' => 'active',
        ]);

        WellnessSurvey::create([
            'tenant_id' => $tenant1->id,
            'title' => 'Mental Health & Wellbeing Check-In',
            'description' => 'Your mental health matters. Share how you are feeling.',
            'questions' => json_encode([
                ['q' => 'How is your overall wellbeing?', 'type' => 'scale'],
                ['q' => 'Do you have adequate support from your manager?', 'type' => 'scale'],
                ['q' => 'Are you experiencing burnout symptoms?', 'type' => 'yes_no'],
                ['q' => 'What resources would help you?', 'type' => 'text'],
            ]),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'anonymous' => true,
            'status' => 'active',
        ]);
    }
}
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RecruitmentController;
use App\Http\Controllers\Admin\OnboardingController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\TdsController;
use App\Http\Controllers\Admin\BenefitsController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LearningController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\WellnessController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Employee\EmployeeSelfServiceController;
use App\Http\Controllers\Employee\EmployeeLeaveController;
use App\Http\Controllers\Employee\EmployeeAttendanceController;
use App\Http\Controllers\Employee\EmployeePayrollController;

Route::get('/', function () { return redirect()->route('login'); });

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Super Admin
Route::get('/superadmin/dashboard', [DashboardController::class, 'superAdmin'])->name('superadmin.dashboard');
Route::get('/superadmin/tenants', [TenantController::class, 'index'])->name('superadmin.tenants.index');
Route::get('/superadmin/tenants/create', [TenantController::class, 'create'])->name('superadmin.tenants.create');
Route::post('/superadmin/tenants', [TenantController::class, 'store'])->name('superadmin.tenants.store');
Route::get('/superadmin/tenants/{id}/edit', [TenantController::class, 'edit'])->name('superadmin.tenants.edit');
Route::put('/superadmin/tenants/{id}', [TenantController::class, 'update'])->name('superadmin.tenants.update');
Route::delete('/superadmin/tenants/{id}', [TenantController::class, 'destroy'])->name('superadmin.tenants.destroy');

// Admin Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

// Employees
Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
Route::get('/admin/employees/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
Route::get('/admin/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
Route::get('/admin/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
Route::put('/admin/employees/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
Route::delete('/admin/employees/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');

// Recruitment
Route::get('/admin/recruitment', [RecruitmentController::class, 'index'])->name('admin.recruitment.index');
Route::get('/admin/recruitment/jobs/create', [RecruitmentController::class, 'createJob'])->name('admin.recruitment.jobs.create');
Route::post('/admin/recruitment/jobs', [RecruitmentController::class, 'storeJob'])->name('admin.recruitment.jobs.store');
Route::get('/admin/recruitment/jobs/{id}/edit', [RecruitmentController::class, 'editJob'])->name('admin.recruitment.jobs.edit');
Route::put('/admin/recruitment/jobs/{id}', [RecruitmentController::class, 'updateJob'])->name('admin.recruitment.jobs.update');
Route::delete('/admin/recruitment/jobs/{id}', [RecruitmentController::class, 'destroyJob'])->name('admin.recruitment.jobs.destroy');
Route::get('/admin/recruitment/candidates', [RecruitmentController::class, 'candidates'])->name('admin.recruitment.candidates');
Route::get('/admin/recruitment/candidates/{id}', [RecruitmentController::class, 'showCandidate'])->name('admin.recruitment.candidates.show');
Route::put('/admin/recruitment/candidates/{id}/status', [RecruitmentController::class, 'updateCandidateStatus'])->name('admin.recruitment.candidates.status');

// Onboarding
Route::get('/admin/onboarding', [OnboardingController::class, 'index'])->name('admin.onboarding.index');
Route::get('/admin/onboarding/create', [OnboardingController::class, 'create'])->name('admin.onboarding.create');
Route::post('/admin/onboarding', [OnboardingController::class, 'store'])->name('admin.onboarding.store');
Route::get('/admin/onboarding/{id}', [OnboardingController::class, 'show'])->name('admin.onboarding.show');
Route::put('/admin/onboarding/{id}/task', [OnboardingController::class, 'updateTask'])->name('admin.onboarding.task.update');

// Performance
Route::get('/admin/performance', [PerformanceController::class, 'index'])->name('admin.performance.index');
Route::get('/admin/performance/create', [PerformanceController::class, 'create'])->name('admin.performance.create');
Route::post('/admin/performance', [PerformanceController::class, 'store'])->name('admin.performance.store');
Route::get('/admin/performance/{id}', [PerformanceController::class, 'show'])->name('admin.performance.show');
Route::get('/admin/performance/{id}/edit', [PerformanceController::class, 'edit'])->name('admin.performance.edit');
Route::put('/admin/performance/{id}', [PerformanceController::class, 'update'])->name('admin.performance.update');
Route::delete('/admin/performance/{id}', [PerformanceController::class, 'destroy'])->name('admin.performance.destroy');

// Payroll
Route::get('/admin/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
Route::get('/admin/payroll/create', [PayrollController::class, 'create'])->name('admin.payroll.create');
Route::post('/admin/payroll/preview', [PayrollController::class, 'preview'])->name('admin.payroll.preview');
Route::post('/admin/payroll/bulk-generate', [PayrollController::class, 'bulkGenerate'])->name('admin.payroll.bulk-generate');
Route::post('/admin/payroll', [PayrollController::class, 'store'])->name('admin.payroll.store');
Route::get('/admin/payroll/{id}', [PayrollController::class, 'show'])->name('admin.payroll.show');
Route::post('/admin/payroll/{id}/process', [PayrollController::class, 'process'])->name('admin.payroll.process');
Route::get('/admin/payroll/{id}/payslip', [PayrollController::class, 'payslip'])->name('admin.payroll.payslip');
Route::delete('/admin/payroll/{id}', [PayrollController::class, 'destroy'])->name('admin.payroll.destroy');
Route::get('/admin/payroll/holidays/list', [PayrollController::class, 'holidays'])->name('admin.payroll.holidays');
Route::post('/admin/payroll/holidays', [PayrollController::class, 'storeHoliday'])->name('admin.payroll.holidays.store');
Route::delete('/admin/payroll/holidays/{id}', [PayrollController::class, 'destroyHoliday'])->name('admin.payroll.holidays.destroy');

// ── TDS Management ────────────────────────────────────────────────────────
Route::get('/admin/tds', [TdsController::class, 'index'])->name('admin.tds.index');
Route::get('/admin/tds/calculator', [TdsController::class, 'calculator'])->name('admin.tds.calculator');
Route::post('/admin/tds/calculate', [TdsController::class, 'calculate'])->name('admin.tds.calculate');
Route::get('/admin/tds/report', [TdsController::class, 'report'])->name('admin.tds.report');
Route::get('/admin/tds/deductions', [TdsController::class, 'deductions'])->name('admin.tds.deductions');
Route::put('/admin/tds/deductions/{id}', [TdsController::class, 'updateDeduction'])->name('admin.tds.deductions.update');
Route::get('/admin/tds/declare/{employeeId}', [TdsController::class, 'declare'])->name('admin.tds.declare');
Route::post('/admin/tds/declare/{employeeId}', [TdsController::class, 'saveDeclare'])->name('admin.tds.declare.save');
Route::get('/admin/tds/certificate/{employeeId}', [TdsController::class, 'certificate'])->name('admin.tds.certificate');

// Benefits
Route::get('/admin/benefits', [BenefitsController::class, 'index'])->name('admin.benefits.index');
Route::get('/admin/benefits/create', [BenefitsController::class, 'create'])->name('admin.benefits.create');
Route::post('/admin/benefits', [BenefitsController::class, 'store'])->name('admin.benefits.store');
Route::get('/admin/benefits/{id}/edit', [BenefitsController::class, 'edit'])->name('admin.benefits.edit');
Route::put('/admin/benefits/{id}', [BenefitsController::class, 'update'])->name('admin.benefits.update');
Route::delete('/admin/benefits/{id}', [BenefitsController::class, 'destroy'])->name('admin.benefits.destroy');

// Attendance
Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
Route::get('/admin/attendance/report', [AttendanceController::class, 'report'])->name('admin.attendance.report');
Route::put('/admin/attendance/{id}/approve', [AttendanceController::class, 'approve'])->name('admin.attendance.approve');
Route::get('/admin/attendance/shifts', [AttendanceController::class, 'shifts'])->name('admin.attendance.shifts');
Route::post('/admin/attendance/shifts', [AttendanceController::class, 'storeShift'])->name('admin.attendance.shifts.store');

// Leaves
Route::get('/admin/leaves', [LeaveController::class, 'index'])->name('admin.leaves.index');
Route::put('/admin/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('admin.leaves.approve');
Route::put('/admin/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('admin.leaves.reject');
Route::get('/admin/leaves/types', [LeaveController::class, 'types'])->name('admin.leaves.types');
Route::post('/admin/leaves/types', [LeaveController::class, 'storeType'])->name('admin.leaves.types.store');

// Learning
Route::get('/admin/learning', [LearningController::class, 'index'])->name('admin.learning.index');
Route::get('/admin/learning/create', [LearningController::class, 'create'])->name('admin.learning.create');
Route::post('/admin/learning', [LearningController::class, 'store'])->name('admin.learning.store');
Route::get('/admin/learning/{id}', [LearningController::class, 'show'])->name('admin.learning.show');
Route::get('/admin/learning/{id}/edit', [LearningController::class, 'edit'])->name('admin.learning.edit');
Route::put('/admin/learning/{id}', [LearningController::class, 'update'])->name('admin.learning.update');
Route::delete('/admin/learning/{id}', [LearningController::class, 'destroy'])->name('admin.learning.destroy');

// Analytics
Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
Route::get('/admin/analytics/workforce', [AnalyticsController::class, 'workforce'])->name('admin.analytics.workforce');
Route::get('/admin/analytics/payroll', [AnalyticsController::class, 'payroll'])->name('admin.analytics.payroll');
Route::get('/admin/analytics/attendance', [AnalyticsController::class, 'attendance'])->name('admin.analytics.attendance');

// Wellness
Route::get('/admin/wellness', [WellnessController::class, 'index'])->name('admin.wellness.index');
Route::get('/admin/wellness/surveys/create', [WellnessController::class, 'createSurvey'])->name('admin.wellness.surveys.create');
Route::post('/admin/wellness/surveys', [WellnessController::class, 'storeSurvey'])->name('admin.wellness.surveys.store');
Route::get('/admin/wellness/surveys/{id}', [WellnessController::class, 'showSurvey'])->name('admin.wellness.surveys.show');

// Employee Self-Service
Route::get('/employee/dashboard', [EmployeeSelfServiceController::class, 'dashboard'])->name('employee.dashboard');
Route::get('/employee/profile', [EmployeeSelfServiceController::class, 'profile'])->name('employee.profile');
Route::put('/employee/profile', [EmployeeSelfServiceController::class, 'updateProfile'])->name('employee.profile.update');
Route::get('/employee/payslips', [EmployeePayrollController::class, 'index'])->name('employee.payslips');
Route::get('/employee/payslips/{id}', [EmployeePayrollController::class, 'show'])->name('employee.payslips.show');
Route::get('/employee/leaves', [EmployeeLeaveController::class, 'index'])->name('employee.leaves.index');
Route::get('/employee/leaves/create', [EmployeeLeaveController::class, 'create'])->name('employee.leaves.create');
Route::post('/employee/leaves', [EmployeeLeaveController::class, 'store'])->name('employee.leaves.store');
Route::get('/employee/attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance.index');
Route::post('/employee/attendance/checkin', [EmployeeAttendanceController::class, 'checkIn'])->name('employee.attendance.checkin');
Route::post('/employee/attendance/checkout', [EmployeeAttendanceController::class, 'checkOut'])->name('employee.attendance.checkout');
Route::get('/employee/learning', [EmployeeSelfServiceController::class, 'learning'])->name('employee.learning');
Route::post('/employee/learning/{id}/enroll', [EmployeeSelfServiceController::class, 'enroll'])->name('employee.learning.enroll');
Route::get('/employee/wellness', [EmployeeSelfServiceController::class, 'wellness'])->name('employee.wellness');
Route::post('/employee/wellness/survey/{id}/respond', [EmployeeSelfServiceController::class, 'submitSurvey'])->name('employee.wellness.survey.respond');
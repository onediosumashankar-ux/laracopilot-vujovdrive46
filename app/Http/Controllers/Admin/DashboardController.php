<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\JobPosting;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Tenant;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private function authCheck($roles = ['admin', 'hr', 'superadmin'])
    {
        if (!session('hrms_logged_in') || !in_array(session('hrms_role'), $roles)) {
            return redirect()->route('login');
        }
        return null;
    }

    public function admin()
    {
        if ($redirect = $this->authCheck(['admin', 'hr'])) return $redirect;

        $tenantId = session('hrms_tenant_id');
        $totalEmployees = Employee::where('tenant_id', $tenantId)->count();
        $activeEmployees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $openJobs = JobPosting::where('tenant_id', $tenantId)->where('status', 'open')->count();
        $pendingLeaves = LeaveRequest::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $todayAttendance = Attendance::where('tenant_id', $tenantId)->whereDate('check_in', today())->count();
        $thisMonthPayroll = Payroll::where('tenant_id', $tenantId)->whereMonth('created_at', now()->month)->sum('net_salary');
        $recentEmployees = Employee::where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->take(5)->get();
        $recentLeaves = LeaveRequest::with('employee')->where('tenant_id', $tenantId)->where('status', 'pending')->take(5)->get();
        $departmentStats = Employee::where('tenant_id', $tenantId)->selectRaw('department, count(*) as count')->groupBy('department')->get();
        $attendanceRate = $totalEmployees > 0 ? round(($todayAttendance / $totalEmployees) * 100) : 0;

        return view('admin.dashboard', compact(
            'totalEmployees', 'activeEmployees', 'openJobs', 'pendingLeaves',
            'todayAttendance', 'thisMonthPayroll', 'recentEmployees', 'recentLeaves',
            'departmentStats', 'attendanceRate'
        ));
    }

    public function superAdmin()
    {
        if ($redirect = $this->authCheck(['superadmin'])) return $redirect;

        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalEmployees = Employee::count();
        $totalUsers = \App\Models\User::count();
        $recentTenants = Tenant::orderBy('created_at', 'desc')->take(5)->get();
        $tenantStats = Tenant::withCount('employees')->orderBy('employees_count', 'desc')->take(10)->get();

        return view('superadmin.dashboard', compact(
            'totalTenants', 'activeTenants', 'totalEmployees', 'totalUsers',
            'recentTenants', 'tenantStats'
        ));
    }
}
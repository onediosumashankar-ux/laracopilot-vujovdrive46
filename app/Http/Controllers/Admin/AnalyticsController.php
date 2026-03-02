<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || !in_array(session('hrms_role'), ['admin', 'hr'])) {
            return redirect()->route('login');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $totalEmployees = Employee::where('tenant_id', $tenantId)->count();
        $byDepartment = Employee::where('tenant_id', $tenantId)->selectRaw('department, count(*) as count')->groupBy('department')->get();
        $byType = Employee::where('tenant_id', $tenantId)->selectRaw('employment_type, count(*) as count')->groupBy('employment_type')->get();
        $avgSalary = Employee::where('tenant_id', $tenantId)->avg('salary');
        $avgPerformance = PerformanceReview::where('tenant_id', $tenantId)->avg('overall_rating');
        $leaveStats = LeaveRequest::where('tenant_id', $tenantId)->selectRaw('status, count(*) as count')->groupBy('status')->get();
        $monthlyPayroll = Payroll::where('tenant_id', $tenantId)->selectRaw('MONTH(created_at) as month, SUM(net_salary) as total')->groupBy('month')->orderBy('month')->get();
        return view('admin.analytics.index', compact('totalEmployees', 'byDepartment', 'byType', 'avgSalary', 'avgPerformance', 'leaveStats', 'monthlyPayroll'));
    }

    public function workforce()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $employees = Employee::where('tenant_id', $tenantId)->get();
        $turnoverRate = 0;
        $totalHired = Employee::where('tenant_id', $tenantId)->whereYear('hire_date', now()->year)->count();
        $byGender = Employee::where('tenant_id', $tenantId)->selectRaw('gender, count(*) as count')->groupBy('gender')->get();
        return view('admin.analytics.workforce', compact('employees', 'turnoverRate', 'totalHired', 'byGender'));
    }

    public function payroll()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $payrolls = Payroll::with('employee')->where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->get();
        $totalGross = $payrolls->sum('gross_salary');
        $totalNet = $payrolls->sum('net_salary');
        $totalTax = $payrolls->sum('tax');
        return view('admin.analytics.payroll', compact('payrolls', 'totalGross', 'totalNet', 'totalTax'));
    }

    public function attendance()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        $attendances = Attendance::with('employee')->where('tenant_id', $tenantId)->whereMonth('check_in', $month)->whereYear('check_in', $year)->get();
        $avgHours = $attendances->avg('hours_worked');
        $lateCount = $attendances->where('is_late', true)->count();
        return view('admin.analytics.attendance', compact('attendances', 'avgHours', 'lateCount', 'month', 'year'));
    }
}
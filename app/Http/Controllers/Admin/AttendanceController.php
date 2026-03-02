<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;

class AttendanceController extends Controller
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
        $attendances = Attendance::with('employee')
            ->where('tenant_id', $tenantId)
            ->whereDate('check_in', today())
            ->orderBy('check_in', 'desc')
            ->paginate(20);
        $totalPresent = Attendance::where('tenant_id', $tenantId)->whereDate('check_in', today())->count();
        $totalEmployees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $lateCount = Attendance::where('tenant_id', $tenantId)->whereDate('check_in', today())->where('is_late', true)->count();
        return view('admin.attendance.index', compact('attendances', 'totalPresent', 'totalEmployees', 'lateCount'));
    }

    public function report()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        $attendances = Attendance::with('employee')
            ->where('tenant_id', $tenantId)
            ->whereMonth('check_in', $month)
            ->whereYear('check_in', $year)
            ->orderBy('check_in', 'desc')
            ->paginate(30);
        return view('admin.attendance.report', compact('attendances', 'month', 'year'));
    }

    public function approve($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $attendance = Attendance::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $attendance->update(['approved' => true, 'approved_by' => session('hrms_user_id')]);
        return redirect()->back()->with('success', 'Attendance approved.');
    }

    public function shifts()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $shifts = Shift::where('tenant_id', session('hrms_tenant_id'))->get();
        return view('admin.attendance.shifts', compact('shifts'));
    }

    public function storeShift(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'grace_period' => 'required|integer|min:0|max:60',
        ]);
        Shift::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.attendance.shifts')->with('success', 'Shift created.');
    }
}
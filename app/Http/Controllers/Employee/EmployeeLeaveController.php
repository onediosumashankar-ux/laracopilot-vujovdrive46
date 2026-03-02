<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeLeaveController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || session('hrms_role') !== 'employee') {
            return redirect()->route('login');
        }
        return null;
    }

    private function getEmployee()
    {
        return Employee::where('tenant_id', session('hrms_tenant_id'))->where('email', session('hrms_user_email'))->first();
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $leaves = LeaveRequest::with('leaveType')->where('employee_id', $employee->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('employee.leaves.index', compact('leaves'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $leaveTypes = LeaveType::where('tenant_id', session('hrms_tenant_id'))->get();
        return view('employee.leaves.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);
        $days = \Carbon\Carbon::parse($validated['start_date'])->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
        LeaveRequest::create(array_merge($validated, [
            'tenant_id' => session('hrms_tenant_id'),
            'employee_id' => $employee->id,
            'days' => $days,
            'status' => 'pending',
        ]));
        return redirect()->route('employee.leaves.index')->with('success', 'Leave request submitted.');
    }
}
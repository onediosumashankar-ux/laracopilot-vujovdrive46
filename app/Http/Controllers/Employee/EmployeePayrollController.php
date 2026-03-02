<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;

class EmployeePayrollController extends Controller
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
        $payslips = Payroll::where('employee_id', $employee->id)->orderBy('created_at', 'desc')->paginate(12);
        return view('employee.payslips.index', compact('payslips', 'employee'));
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $payslip = Payroll::where('employee_id', $employee->id)->findOrFail($id);
        return view('employee.payslips.show', compact('payslip', 'employee'));
    }
}
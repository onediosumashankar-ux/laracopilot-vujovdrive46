<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Holiday;
use App\Services\PayrollCalculationService;
use Illuminate\Http\Request;

class PayrollController extends Controller
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
        $payrolls = Payroll::with('employee')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $totalPaid = Payroll::where('tenant_id', $tenantId)->where('status', 'paid')->sum('net_salary');
        $pendingPayroll = Payroll::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        return view('admin.payroll.index', compact('payrolls', 'totalPaid', 'pendingPayroll', 'currentMonth', 'currentYear'));
    }

    /**
     * Show payroll generation form with month/year + employee selector.
     */
    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employees = Employee::where('tenant_id', session('hrms_tenant_id'))
            ->where('status', 'active')
            ->get();
        $months = [
            1=>'January',2=>'February',3=>'March',4=>'April',
            5=>'May',6=>'June',7=>'July',8=>'August',
            9=>'September',10=>'October',11=>'November',12=>'December',
        ];
        return view('admin.payroll.create', compact('employees', 'months'));
    }

    /**
     * Preview calculated payroll before saving.
     */
    public function preview(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer|min:2000|max:2099',
        ]);

        $employee = Employee::where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($request->employee_id);

        $service = new PayrollCalculationService();
        $calculation = $service->calculate($employee, $request->month, $request->year);

        return view('admin.payroll.preview', compact('employee', 'calculation', 'request'));
    }

    /**
     * Generate bulk payroll for all active employees.
     */
    public function bulkGenerate(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2099',
        ]);

        $tenantId = session('hrms_tenant_id');
        $employees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $service = new PayrollCalculationService();
        $generated = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Skip if already generated for this period
            $exists = Payroll::where('employee_id', $employee->id)
                ->whereMonth('pay_period_start', $request->month)
                ->whereYear('pay_period_start', $request->year)
                ->exists();
            if ($exists) { $skipped++; continue; }

            $calc = $service->calculate($employee, $request->month, $request->year);
            Payroll::create([
                'tenant_id'          => $tenantId,
                'employee_id'        => $employee->id,
                'pay_period_start'   => $calc['pay_period_start'],
                'pay_period_end'     => $calc['pay_period_end'],
                'working_days'       => $calc['working_days'],
                'present_days'       => $calc['present_days'],
                'absent_days'        => $calc['absent_days'],
                'late_days'          => $calc['late_days'],
                'holiday_days'       => $calc['holiday_days'],
                'leave_days'         => $calc['leave_days'],
                'half_days'          => $calc['half_days'],
                'per_day_salary'     => $calc['per_day_salary'],
                'basic_salary'       => $calc['basic_salary'],
                'allowances'         => $calc['allowances'],
                'bonus'              => $calc['bonus'],
                'overtime_pay'       => $calc['overtime_pay'],
                'gross_salary'       => $calc['gross_salary'],
                'absence_deduction'  => $calc['absence_deduction'],
                'late_deduction'     => $calc['late_deduction'],
                'deductions'         => $calc['deductions'],
                'tax'                => $calc['tax'],
                'net_salary'         => $calc['net_salary'],
                'status'             => 'pending',
                'notes'              => 'Auto-generated. Absent: '.$calc['absent_days'].'d, Late: '.$calc['late_days'].'d, Leave: '.$calc['leave_days'].'d, Holidays: '.$calc['holiday_days'].'d',
            ]);
            $generated++;
        }

        return redirect()->route('admin.payroll.index')
            ->with('success', "Bulk payroll generated: {$generated} created, {$skipped} skipped (already exists).");
    }

    /**
     * Store single employee payroll after preview.
     */
    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $validated = $request->validate([
            'employee_id'        => 'required|exists:employees,id',
            'pay_period_start'   => 'required|date',
            'pay_period_end'     => 'required|date',
            'working_days'       => 'required|integer',
            'present_days'       => 'required|integer',
            'absent_days'        => 'required|integer',
            'late_days'          => 'required|integer',
            'holiday_days'       => 'required|integer',
            'leave_days'         => 'required|integer',
            'half_days'          => 'required|integer',
            'per_day_salary'     => 'required|numeric',
            'basic_salary'       => 'required|numeric',
            'allowances'         => 'nullable|numeric',
            'bonus'              => 'nullable|numeric',
            'overtime_pay'       => 'nullable|numeric',
            'gross_salary'       => 'required|numeric',
            'absence_deduction'  => 'nullable|numeric',
            'late_deduction'     => 'nullable|numeric',
            'deductions'         => 'nullable|numeric',
            'tax'                => 'nullable|numeric',
            'net_salary'         => 'required|numeric',
            'notes'              => 'nullable|string',
        ]);

        Payroll::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'status'    => 'pending',
        ]));

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll record created successfully.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $payroll = Payroll::with('employee')
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);
        return view('admin.payroll.show', compact('payroll'));
    }

    public function process($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $payroll = Payroll::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $payroll->update([
            'status'       => 'paid',
            'paid_at'      => now(),
            'processed_by' => session('hrms_user_id'),
        ]);
        return redirect()->back()->with('success', 'Payroll processed and marked as paid.');
    }

    public function payslip($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $payroll = Payroll::with('employee.tenant')
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);
        return view('admin.payroll.payslip', compact('payroll'));
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        Payroll::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.payroll.index')->with('success', 'Payroll entry deleted.');
    }

    // ── Holiday Management ────────────────────────────────────────────────

    public function holidays()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $holidays = Holiday::where('tenant_id', session('hrms_tenant_id'))
            ->orderBy('date')
            ->paginate(20);
        return view('admin.payroll.holidays', compact('holidays'));
    }

    public function storeHoliday(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'date'        => 'required|date',
            'type'        => 'required|in:public,company,optional',
            'description' => 'nullable|string',
            'recurring'   => 'boolean',
        ]);
        Holiday::create(array_merge($validated, [
            'tenant_id' => session('hrms_tenant_id'),
            'recurring' => $request->boolean('recurring'),
        ]));
        return redirect()->route('admin.payroll.holidays')->with('success', 'Holiday added.');
    }

    public function destroyHoliday($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        Holiday::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.payroll.holidays')->with('success', 'Holiday removed.');
    }
}
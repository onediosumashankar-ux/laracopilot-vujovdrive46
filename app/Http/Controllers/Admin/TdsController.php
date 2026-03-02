<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TdsDeclaration;
use App\Models\TdsDeduction;
use App\Models\Employee;
use App\Services\TdsCalculationService;
use Illuminate\Http\Request;

class TdsController extends Controller
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
        $fy = (new TdsCalculationService())->getFinancialYear(now()->month, now()->year);

        $declarations = TdsDeclaration::with('employee')
            ->where('tenant_id', $tenantId)
            ->where('financial_year', $fy)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalTdsLiability = TdsDeclaration::where('tenant_id', $tenantId)
            ->where('financial_year', $fy)->sum('total_tax_liability');
        $totalMonthlyTds = TdsDeclaration::where('tenant_id', $tenantId)
            ->where('financial_year', $fy)->sum('monthly_tds');
        $employeesWithDeclaration = TdsDeclaration::where('tenant_id', $tenantId)
            ->where('financial_year', $fy)->count();
        $totalEmployees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->count();

        $deductions = TdsDeduction::with('employee')
            ->where('tenant_id', $tenantId)
            ->orderBy('year', 'desc')->orderBy('month', 'desc')
            ->take(10)->get();

        return view('admin.tds.index', compact(
            'declarations', 'totalTdsLiability', 'totalMonthlyTds',
            'employeesWithDeclaration', 'totalEmployees', 'deductions', 'fy'
        ));
    }

    public function declare($employeeId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($employeeId);
        $fy = (new TdsCalculationService())->getFinancialYear(now()->month, now()->year);
        $existing = TdsDeclaration::where('employee_id', $employeeId)->where('financial_year', $fy)->first();
        return view('admin.tds.declare', compact('employee', 'fy', 'existing'));
    }

    public function saveDeclare(Request $request, $employeeId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($employeeId);
        $request->validate([
            'tax_regime'         => 'required|in:old,new',
            'financial_year'     => 'required|string',
            'section_80c'        => 'nullable|numeric|min:0|max:150000',
            'section_80ccd1b'    => 'nullable|numeric|min:0|max:50000',
            'section_80d'        => 'nullable|numeric|min:0|max:50000',
            'section_80e'        => 'nullable|numeric|min:0',
            'section_80g'        => 'nullable|numeric|min:0',
            'section_80tta'      => 'nullable|numeric|min:0|max:10000',
            'section_24b'        => 'nullable|numeric|min:0|max:200000',
            'hra_actual'         => 'nullable|numeric|min:0',
            'rent_paid_annual'   => 'nullable|numeric|min:0',
            'lta_exemption'      => 'nullable|numeric|min:0',
            'other_deductions'   => 'nullable|numeric|min:0',
        ]);

        $service = new TdsCalculationService();
        $result  = $service->computeFromDeclaration($request->all(), $employee);

        $fy = $request->financial_year;

        $declaration = TdsDeclaration::updateOrCreate(
            ['employee_id' => $employee->id, 'financial_year' => $fy],
            [
                'tenant_id'              => session('hrms_tenant_id'),
                'tax_regime'             => $request->tax_regime,
                'residential_status'     => $request->residential_status ?? 'resident',
                'is_senior_citizen'      => $request->boolean('is_senior_citizen'),
                'is_super_senior'        => $request->boolean('is_super_senior'),
                'hra_exemption'          => $result['hra_exemption_computed'],
                'lta_exemption'          => $request->lta_exemption ?? 0,
                'section_80c'            => $request->section_80c ?? 0,
                'section_80ccd1b'        => $request->section_80ccd1b ?? 0,
                'section_80d'            => $request->section_80d ?? 0,
                'section_80dd'           => $request->section_80dd ?? 0,
                'section_80e'            => $request->section_80e ?? 0,
                'section_80g'            => $request->section_80g ?? 0,
                'section_80tta'          => $request->section_80tta ?? 0,
                'section_24b'            => $request->section_24b ?? 0,
                'other_deductions'       => $request->other_deductions ?? 0,
                'hra_actual'             => $request->hra_actual ?? 0,
                'basic_salary_annual'    => $employee->salary * 0.5,
                'rent_paid_annual'       => $request->rent_paid_annual ?? 0,
                'metro_city'             => $request->boolean('metro_city'),
                'gross_annual_income'    => $result['gross_annual_income'],
                'total_exemptions'       => $result['total_exemptions'],
                'total_deductions'       => $result['total_deductions'],
                'taxable_income'         => $result['taxable_income'],
                'annual_tax'             => $result['annual_tax'],
                'surcharge'              => $result['surcharge'],
                'health_education_cess'  => $result['health_education_cess'],
                'total_tax_liability'    => $result['total_tax_liability'],
                'monthly_tds'            => $result['monthly_tds'],
                'notes'                  => $request->notes,
            ]
        );

        return redirect()->route('admin.tds.index')
            ->with('success', 'TDS declaration saved. Monthly TDS: ₹' . number_format($result['monthly_tds'], 2));
    }

    public function calculator()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employees = Employee::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        return view('admin.tds.calculator', compact('employees'));
    }

    public function calculate(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tax_regime'  => 'required|in:old,new',
        ]);
        $employee = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($request->employee_id);
        $service  = new TdsCalculationService();
        $result   = $service->computeFromDeclaration($request->all(), $employee);
        $fy       = $service->getFinancialYear(now()->month, now()->year);
        return view('admin.tds.result', compact('employee', 'result', 'request', 'fy'));
    }

    public function certificate($employeeId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee    = Employee::where('tenant_id', session('hrms_tenant_id'))->with('tenant')->findOrFail($employeeId);
        $fy          = (new TdsCalculationService())->getFinancialYear(now()->month, now()->year);
        $declaration = TdsDeclaration::where('employee_id', $employeeId)->where('financial_year', $fy)->first();
        $deductions  = TdsDeduction::where('employee_id', $employeeId)->where('financial_year', $fy)->orderBy('month')->get();
        $totalTdsDeducted = $deductions->sum('total_tds');
        return view('admin.tds.certificate', compact('employee', 'declaration', 'deductions', 'totalTdsDeducted', 'fy'));
    }

    public function report()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $fy = request('fy', (new TdsCalculationService())->getFinancialYear(now()->month, now()->year));
        $declarations = TdsDeclaration::with('employee')->where('tenant_id', $tenantId)->where('financial_year', $fy)->get();
        $deductions   = TdsDeduction::with('employee')->where('tenant_id', $tenantId)->where('financial_year', $fy)->orderBy('month')->get();
        $monthlyTotals = $deductions->groupBy('month')->map(fn($g) => $g->sum('total_tds'));
        return view('admin.tds.report', compact('declarations', 'deductions', 'monthlyTotals', 'fy'));
    }

    public function deductions()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $deductions = TdsDeduction::with('employee')
            ->where('tenant_id', $tenantId)
            ->orderBy('year', 'desc')->orderBy('month', 'desc')
            ->paginate(20);
        return view('admin.tds.deductions', compact('deductions'));
    }

    public function updateDeduction(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $deduction = TdsDeduction::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $deduction->update([
            'status'         => $request->status,
            'challan_number' => $request->challan_number,
            'deduction_date' => $request->deduction_date,
        ]);
        return redirect()->back()->with('success', 'TDS deduction status updated.');
    }
}
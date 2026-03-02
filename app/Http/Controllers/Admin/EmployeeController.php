<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
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

        $branchFilter = request('branch_id');
        $deptFilter   = request('department');
        $statusFilter = request('status', 'active');

        $query = Employee::with('branch')
            ->where('tenant_id', $tenantId);

        if ($branchFilter) $query->where('branch_id', $branchFilter);
        if ($deptFilter)   $query->where('department', $deptFilter);
        if ($statusFilter) $query->where('status', $statusFilter);

        $employees  = $query->orderBy('first_name')->paginate(15)->withQueryString();
        $branches   = Branch::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();
        $departments = Employee::where('tenant_id', $tenantId)->distinct()->pluck('department');

        return view('admin.employees.index', compact('employees', 'branches', 'departments', 'branchFilter', 'deptFilter', 'statusFilter'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $branches = Branch::where('tenant_id', session('hrms_tenant_id'))
            ->where('is_active', true)
            ->orderByDesc('is_head_office')
            ->orderBy('name')
            ->get();
        return view('admin.employees.create', compact('branches'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'branch_id'       => 'required|exists:branches,id',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|unique:employees,email',
            'phone'           => 'nullable|string|max:20',
            'department'      => 'required|string|max:100',
            'position'        => 'required|string|max:150',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status'          => 'required|in:active,inactive,terminated',
            'salary'          => 'required|numeric|min:0',
            'hire_date'       => 'required|date',
            'date_of_birth'   => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'address'         => 'nullable|string|max:500',
            'bank_account'    => 'nullable|string|max:100',
            'tax_id'          => 'nullable|string|max:20',
            'manager'         => 'nullable|string|max:150',
        ]);

        Employee::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.employees.index')->with('success', 'Employee added successfully.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = Employee::with(['branch', 'currentSalaryStructure.salaryStructure', 'currentSalaryStructure.breakdowns'])
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $branches = Branch::where('tenant_id', session('hrms_tenant_id'))
            ->where('is_active', true)
            ->orderByDesc('is_head_office')
            ->orderBy('name')
            ->get();
        return view('admin.employees.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee  = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'branch_id'       => 'required|exists:branches,id',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|unique:employees,email,' . $id,
            'phone'           => 'nullable|string|max:20',
            'department'      => 'required|string|max:100',
            'position'        => 'required|string|max:150',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status'          => 'required|in:active,inactive,terminated',
            'salary'          => 'required|numeric|min:0',
            'hire_date'       => 'required|date',
            'date_of_birth'   => 'nullable|date',
            'gender'          => 'nullable|in:male,female,other',
            'address'         => 'nullable|string|max:500',
            'bank_account'    => 'nullable|string|max:100',
            'tax_id'          => 'nullable|string|max:20',
            'manager'         => 'nullable|string|max:150',
        ]);

        $employee->update($validated);
        return redirect()->route('admin.employees.show', $id)->with('success', 'Employee updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted.');
    }
}
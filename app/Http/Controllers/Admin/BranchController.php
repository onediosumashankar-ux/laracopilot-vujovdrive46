<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;

class BranchController extends Controller
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

        $branches = Branch::where('tenant_id', $tenantId)
            ->withCount(['employees' => fn($q) => $q->where('status', 'active')])
            ->orderByDesc('is_head_office')
            ->orderBy('name')
            ->get();

        $totalEmployees  = Employee::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $totalBranches   = $branches->count();
        $activeBranches  = $branches->where('is_active', true)->count();
        $headOffice      = $branches->firstWhere('is_head_office', true);

        return view('admin.branches.index', compact(
            'branches', 'totalEmployees', 'totalBranches', 'activeBranches', 'headOffice'
        ));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $hasHeadOffice = Branch::where('tenant_id', session('hrms_tenant_id'))
            ->where('is_head_office', true)->exists();
        return view('admin.branches.create', compact('hasHeadOffice'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'pincode'       => 'nullable|string|max:10',
            'country'       => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'manager_name'  => 'nullable|string|max:255',
            'is_head_office'=> 'boolean',
            'is_active'     => 'boolean',
        ]);

        // Only one head office allowed
        if ($request->boolean('is_head_office')) {
            Branch::where('tenant_id', $tenantId)->update(['is_head_office' => false]);
        }

        Branch::create(array_merge($validated, [
            'tenant_id'      => $tenantId,
            'is_head_office' => $request->boolean('is_head_office'),
            'is_active'      => $request->boolean('is_active', true),
            'country'        => $request->country ?? 'India',
        ]));

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $branch    = Branch::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $employees = Employee::where('branch_id', $id)
            ->where('tenant_id', session('hrms_tenant_id'))
            ->orderBy('department')
            ->get();

        $deptStats = $employees->groupBy('department')
            ->map(fn($g) => $g->count());

        $activeCount   = $employees->where('status', 'active')->count();
        $inactiveCount = $employees->where('status', '!=', 'active')->count();

        return view('admin.branches.show', compact(
            'branch', 'employees', 'deptStats', 'activeCount', 'inactiveCount'
        ));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $branch        = Branch::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $hasHeadOffice = Branch::where('tenant_id', session('hrms_tenant_id'))
            ->where('is_head_office', true)
            ->where('id', '!=', $id)
            ->exists();
        return view('admin.branches.edit', compact('branch', 'hasHeadOffice'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $branch   = Branch::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'pincode'      => 'nullable|string|max:10',
            'country'      => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
        ]);

        if ($request->boolean('is_head_office')) {
            Branch::where('tenant_id', $tenantId)->where('id', '!=', $id)->update(['is_head_office' => false]);
        }

        $branch->update(array_merge($validated, [
            'is_head_office' => $request->boolean('is_head_office'),
            'is_active'      => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.branches.show', $id)
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $branch = Branch::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);

        if ($branch->employees()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete branch with assigned employees. Reassign them first.']);
        }

        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted.');
    }

    // Reassign employees from one branch to another
    public function reassign(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'target_branch_id' => 'required|exists:branches,id',
            'employee_ids'     => 'required|array',
            'employee_ids.*'   => 'exists:employees,id',
        ]);

        Employee::whereIn('id', $request->employee_ids)
            ->where('tenant_id', session('hrms_tenant_id'))
            ->update(['branch_id' => $request->target_branch_id]);

        return redirect()->back()
            ->with('success', count($request->employee_ids) . ' employee(s) reassigned successfully.');
    }
}
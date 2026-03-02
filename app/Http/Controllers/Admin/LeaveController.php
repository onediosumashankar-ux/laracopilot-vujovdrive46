<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
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
        $leaves = LeaveRequest::with(['employee', 'leaveType'])->where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->paginate(15);
        $pendingCount = LeaveRequest::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $approvedCount = LeaveRequest::where('tenant_id', $tenantId)->where('status', 'approved')->count();
        $rejectedCount = LeaveRequest::where('tenant_id', $tenantId)->where('status', 'rejected')->count();
        return view('admin.leaves.index', compact('leaves', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function approve($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $leave = LeaveRequest::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $leave->update(['status' => 'approved', 'approved_by' => session('hrms_user_id'), 'approved_at' => now()]);
        return redirect()->back()->with('success', 'Leave approved successfully.');
    }

    public function reject($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $leave = LeaveRequest::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $leave->update(['status' => 'rejected', 'approved_by' => session('hrms_user_id'), 'approved_at' => now()]);
        return redirect()->back()->with('success', 'Leave rejected.');
    }

    public function types()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $types = LeaveType::where('tenant_id', session('hrms_tenant_id'))->get();
        return view('admin.leaves.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'days_allowed' => 'required|integer|min:1',
            'carry_forward' => 'boolean',
            'paid' => 'boolean',
        ]);
        LeaveType::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.leaves.types')->with('success', 'Leave type created.');
    }
}
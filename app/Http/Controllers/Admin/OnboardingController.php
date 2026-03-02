<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingPlan;
use App\Models\OnboardingTask;
use App\Models\Employee;
use Illuminate\Http\Request;

class OnboardingController extends Controller
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
        $plans = OnboardingPlan::with('employee')->where('tenant_id', session('hrms_tenant_id'))->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.onboarding.index', compact('plans'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employees = Employee::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        return view('admin.onboarding.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'buddy_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.due_date' => 'required|date',
            'tasks.*.assigned_to' => 'nullable|string|max:255',
        ]);

        $plan = OnboardingPlan::create([
            'tenant_id' => session('hrms_tenant_id'),
            'employee_id' => $validated['employee_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'buddy_name' => $validated['buddy_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'in_progress',
        ]);

        foreach ($validated['tasks'] as $task) {
            OnboardingTask::create([
                'onboarding_plan_id' => $plan->id,
                'title' => $task['title'],
                'due_date' => $task['due_date'],
                'assigned_to' => $task['assigned_to'] ?? null,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.onboarding.index')->with('success', 'Onboarding plan created.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $plan = OnboardingPlan::with(['employee', 'tasks'])->where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.onboarding.show', compact('plan'));
    }

    public function updateTask(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $task = OnboardingTask::findOrFail($id);
        $task->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Task updated.');
    }
}
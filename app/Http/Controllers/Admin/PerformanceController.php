<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use App\Models\Employee;
use Illuminate\Http\Request;

class PerformanceController extends Controller
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
        $reviews = PerformanceReview::with('employee')->where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.performance.index', compact('reviews'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employees = Employee::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        return view('admin.performance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_period' => 'required|string|max:100',
            'review_date' => 'required|date',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'quality_of_work' => 'required|numeric|min:1|max:5',
            'productivity' => 'required|numeric|min:1|max:5',
            'teamwork' => 'required|numeric|min:1|max:5',
            'communication' => 'required|numeric|min:1|max:5',
            'attendance_rating' => 'required|numeric|min:1|max:5',
            'goals_achieved' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'feedback' => 'nullable|string',
            'status' => 'required|in:draft,completed,acknowledged',
        ]);
        PerformanceReview::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id'), 'reviewer_id' => session('hrms_user_id')]));
        return redirect()->route('admin.performance.index')->with('success', 'Performance review created.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $review = PerformanceReview::with('employee')->where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.performance.show', compact('review'));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $review = PerformanceReview::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $employees = Employee::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        return view('admin.performance.edit', compact('review', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $review = PerformanceReview::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'review_period' => 'required|string|max:100',
            'review_date' => 'required|date',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'quality_of_work' => 'required|numeric|min:1|max:5',
            'productivity' => 'required|numeric|min:1|max:5',
            'teamwork' => 'required|numeric|min:1|max:5',
            'communication' => 'required|numeric|min:1|max:5',
            'attendance_rating' => 'required|numeric|min:1|max:5',
            'goals_achieved' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'feedback' => 'nullable|string',
            'status' => 'required|in:draft,completed,acknowledged',
        ]);
        $review->update($validated);
        return redirect()->route('admin.performance.index')->with('success', 'Performance review updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        PerformanceReview::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.performance.index')->with('success', 'Review deleted.');
    }
}
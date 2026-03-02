<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;

class LearningController extends Controller
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
        $programs = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->withCount('enrollments')->orderBy('created_at', 'desc')->paginate(12);
        return view('admin.learning.index', compact('programs'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.learning.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,soft_skills,compliance,leadership,onboarding,other',
            'delivery_mode' => 'required|in:online,classroom,blended,self_paced',
            'duration_hours' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'instructor' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);
        TrainingProgram::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.learning.index')->with('success', 'Training program created.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::with('enrollments.employee')->where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.learning.show', compact('program'));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.learning.edit', compact('program'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,soft_skills,compliance,leadership,onboarding,other',
            'delivery_mode' => 'required|in:online,classroom,blended,self_paced',
            'duration_hours' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'instructor' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);
        $program->update($validated);
        return redirect()->route('admin.learning.index')->with('success', 'Training program updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.learning.index')->with('success', 'Training program deleted.');
    }
}
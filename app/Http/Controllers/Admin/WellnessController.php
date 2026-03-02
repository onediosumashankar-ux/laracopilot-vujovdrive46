<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WellnessSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class WellnessController extends Controller
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
        $surveys = WellnessSurvey::where('tenant_id', session('hrms_tenant_id'))->withCount('responses')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.wellness.index', compact('surveys'));
    }

    public function createSurvey()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.wellness.create-survey');
    }

    public function storeSurvey(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'anonymous' => 'boolean',
            'status' => 'required|in:draft,active,closed',
        ]);
        WellnessSurvey::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.wellness.index')->with('success', 'Survey created.');
    }

    public function showSurvey($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $survey = WellnessSurvey::with('responses.employee')->where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.wellness.show-survey', compact('survey'));
    }
}
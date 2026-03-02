<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\Candidate;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
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
        $jobs = JobPosting::where('tenant_id', $tenantId)->withCount('candidates')->orderBy('created_at', 'desc')->paginate(10);
        $totalCandidates = Candidate::where('tenant_id', $tenantId)->count();
        $openJobs = JobPosting::where('tenant_id', $tenantId)->where('status', 'open')->count();
        $shortlisted = Candidate::where('tenant_id', $tenantId)->where('status', 'shortlisted')->count();
        return view('admin.recruitment.index', compact('jobs', 'totalCandidates', 'openJobs', 'shortlisted'));
    }

    public function createJob()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.recruitment.create-job');
    }

    public function storeJob(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'type' => 'required|in:full_time,part_time,contract,remote',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:open,closed,draft',
            'vacancies' => 'required|integer|min:1',
        ]);
        JobPosting::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.recruitment.index')->with('success', 'Job posting created successfully.');
    }

    public function editJob($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $job = JobPosting::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.recruitment.edit-job', compact('job'));
    }

    public function updateJob(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $job = JobPosting::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'type' => 'required|in:full_time,part_time,contract,remote',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'deadline' => 'required|date',
            'status' => 'required|in:open,closed,draft',
            'vacancies' => 'required|integer|min:1',
        ]);
        $job->update($validated);
        return redirect()->route('admin.recruitment.index')->with('success', 'Job posting updated.');
    }

    public function destroyJob($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        JobPosting::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.recruitment.index')->with('success', 'Job posting deleted.');
    }

    public function candidates()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $candidates = Candidate::with('jobPosting')->where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->paginate(15);
        $jobs = JobPosting::where('tenant_id', $tenantId)->get();
        return view('admin.recruitment.candidates', compact('candidates', 'jobs'));
    }

    public function showCandidate($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $candidate = Candidate::with('jobPosting')->where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.recruitment.show-candidate', compact('candidate'));
    }

    public function updateCandidateStatus(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $candidate = Candidate::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $request->validate(['status' => 'required|in:applied,screening,interview,shortlisted,offered,hired,rejected']);
        $candidate->update(['status' => $request->status, 'notes' => $request->notes]);
        return redirect()->back()->with('success', 'Candidate status updated.');
    }
}
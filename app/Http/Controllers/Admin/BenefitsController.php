<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Employee;
use Illuminate\Http\Request;

class BenefitsController extends Controller
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
        $benefits = Benefit::where('tenant_id', session('hrms_tenant_id'))->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.benefits.index', compact('benefits'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.benefits.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:health_insurance,dental,vision,life_insurance,retirement,transport,meal,housing,education,other',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,annually,one_time',
            'eligibility' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        Benefit::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.benefits.index')->with('success', 'Benefit created successfully.');
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $benefit = Benefit::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.benefits.edit', compact('benefit'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $benefit = Benefit::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:health_insurance,dental,vision,life_insurance,retirement,transport,meal,housing,education,other',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,annually,one_time',
            'eligibility' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $benefit->update($validated);
        return redirect()->route('admin.benefits.index')->with('success', 'Benefit updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        Benefit::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.benefits.index')->with('success', 'Benefit deleted.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalaryStructure;
use App\Models\Employee;
use App\Models\OfferLetter;
use App\Models\Candidate;
use App\Services\SalaryStructureService;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || !in_array(session('hrms_role'), ['admin', 'hr'])) {
            return redirect()->route('login');
        }
        return null;
    }

    // ── Salary Structure Templates ────────────────────────────────────────

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $structures = SalaryStructure::where('tenant_id', session('hrms_tenant_id'))
            ->withCount(['components', 'employeeAssignments'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return view('admin.salary.index', compact('structures'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.salary.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type'        => 'required|in:monthly,annual',
            'ctc_amount'  => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);
        $structure = SalaryStructure::create(array_merge($validated, [
            'tenant_id' => session('hrms_tenant_id'),
            'is_active' => $request->boolean('is_active', true),
        ]));
        return redirect()->route('admin.salary.show', $structure->id)
            ->with('success', 'Salary structure created. Now add components.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $structure = SalaryStructure::with('components')
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);

        // Preview computation with default CTC
        $service   = new SalaryStructureService();
        $preview   = $structure->activeComponents->count() > 0
            ? $service->compute($structure, (float)$structure->ctc_amount)
            : null;

        $assignments = EmployeeSalaryStructure::with('employee')
            ->where('salary_structure_id', $id)
            ->where('is_current', true)
            ->get();

        return view('admin.salary.show', compact('structure', 'preview', 'assignments'));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $structure = SalaryStructure::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.salary.edit', compact('structure'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $structure = SalaryStructure::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type'        => 'required|in:monthly,annual',
            'ctc_amount'  => 'required|numeric|min:0',
        ]);
        $structure->update(array_merge($validated, ['is_active' => $request->boolean('is_active', true)]));
        return redirect()->route('admin.salary.show', $id)->with('success', 'Structure updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        SalaryStructure::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.salary.index')->with('success', 'Structure deleted.');
    }

    // ── Components ────────────────────────────────────────────────────────

    public function storeComponent(Request $request, $structureId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        SalaryStructure::where('tenant_id', session('hrms_tenant_id'))->findOrFail($structureId);
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'code'             => 'required|string|max:30',
            'type'             => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage_basic,percentage_ctc,percentage_gross,formula',
            'value'            => 'required|numeric|min:0',
            'formula'          => 'nullable|string',
            'taxable'          => 'boolean',
            'pf_applicable'    => 'boolean',
            'esi_applicable'   => 'boolean',
            'max_limit'        => 'nullable|numeric|min:0',
            'sort_order'       => 'nullable|integer',
        ]);
        SalaryComponent::create(array_merge($validated, [
            'salary_structure_id' => $structureId,
            'taxable'             => $request->boolean('taxable', true),
            'pf_applicable'       => $request->boolean('pf_applicable'),
            'esi_applicable'      => $request->boolean('esi_applicable'),
            'sort_order'          => $request->sort_order ?? 0,
        ]));
        return redirect()->route('admin.salary.show', $structureId)
            ->with('success', 'Component added.');
    }

    public function updateComponent(Request $request, $structureId, $componentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $component = SalaryComponent::where('salary_structure_id', $structureId)->findOrFail($componentId);
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'code'             => 'required|string|max:30',
            'type'             => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage_basic,percentage_ctc,percentage_gross,formula',
            'value'            => 'required|numeric|min:0',
            'formula'          => 'nullable|string',
            'max_limit'        => 'nullable|numeric|min:0',
            'sort_order'       => 'nullable|integer',
        ]);
        $component->update(array_merge($validated, [
            'taxable'       => $request->boolean('taxable', true),
            'pf_applicable' => $request->boolean('pf_applicable'),
            'esi_applicable'=> $request->boolean('esi_applicable'),
            'is_active'     => $request->boolean('is_active', true),
        ]));
        return redirect()->route('admin.salary.show', $structureId)->with('success', 'Component updated.');
    }

    public function destroyComponent($structureId, $componentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        SalaryComponent::where('salary_structure_id', $structureId)->findOrFail($componentId)->delete();
        return redirect()->route('admin.salary.show', $structureId)->with('success', 'Component removed.');
    }

    // ── Assign Structure to Employee ──────────────────────────────────────

    public function assignIndex()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId    = session('hrms_tenant_id');
        $assignments = EmployeeSalaryStructure::with(['employee', 'salaryStructure', 'breakdowns'])
            ->where('tenant_id', $tenantId)
            ->where('is_current', true)
            ->orderBy('effective_from', 'desc')
            ->paginate(20);
        $employees  = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $structures = SalaryStructure::where('tenant_id', $tenantId)->where('is_active', true)->get();
        return view('admin.salary.assign', compact('assignments', 'employees', 'structures'));
    }

    public function previewAssignment(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'employee_id'          => 'required|exists:employees,id',
            'salary_structure_id'  => 'required|exists:salary_structures,id',
            'ctc_override'         => 'nullable|numeric|min:0',
        ]);
        $structure = SalaryStructure::with('components')
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($request->salary_structure_id);
        $employee  = Employee::where('tenant_id', session('hrms_tenant_id'))->findOrFail($request->employee_id);
        $ctc       = (float)($request->ctc_override ?: $structure->ctc_amount);
        $service   = new SalaryStructureService();
        $preview   = $service->compute($structure, $ctc);
        return view('admin.salary.preview-assignment', compact('structure', 'employee', 'preview', 'request'));
    }

    public function assign(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $validated = $request->validate([
            'employee_id'         => 'required|exists:employees,id',
            'salary_structure_id' => 'required|exists:salary_structures,id',
            'ctc_override'        => 'nullable|numeric|min:0',
            'effective_from'      => 'required|date',
            'notes'               => 'nullable|string',
        ]);

        $structure = SalaryStructure::findOrFail($validated['salary_structure_id']);
        $ctc       = (float)($validated['ctc_override'] ?? $structure->ctc_amount);

        // Deactivate previous
        EmployeeSalaryStructure::where('employee_id', $validated['employee_id'])
            ->where('is_current', true)
            ->update(['is_current' => false, 'effective_to' => now()->toDateString()]);

        $ess = EmployeeSalaryStructure::create([
            'tenant_id'            => $tenantId,
            'employee_id'          => $validated['employee_id'],
            'salary_structure_id'  => $validated['salary_structure_id'],
            'ctc_override'         => $validated['ctc_override'] ?? null,
            'effective_from'       => $validated['effective_from'],
            'is_current'           => true,
            'notes'                => $validated['notes'] ?? null,
            'created_by'           => session('hrms_user_id'),
        ]);

        // Also update employee salary field
        Employee::where('id', $validated['employee_id'])
            ->update(['salary' => $structure->type === 'annual' ? $ctc : $ctc * 12]);

        $service = new SalaryStructureService();
        $comp    = $service->compute($structure, $ctc);
        $service->saveBreakdown($ess, $comp);

        return redirect()->route('admin.salary.assign')
            ->with('success', 'Salary structure assigned and breakdown saved successfully.');
    }

    public function viewBreakdown($assignmentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $ess = EmployeeSalaryStructure::with(['employee', 'salaryStructure', 'breakdowns'])
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($assignmentId);
        $ctc     = $ess->effective_ctc;
        $service = new SalaryStructureService();
        $preview = $service->compute($ess->salaryStructure, $ctc);
        return view('admin.salary.breakdown', compact('ess', 'preview'));
    }

    // ── Offer Letters ─────────────────────────────────────────────────────

    public function offerLetters()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $offers = OfferLetter::with(['employee', 'candidate'])
            ->where('tenant_id', session('hrms_tenant_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.salary.offer-letters', compact('offers'));
    }

    public function createOffer()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId   = session('hrms_tenant_id');
        $employees  = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $candidates = Candidate::where('tenant_id', $tenantId)->whereIn('status', ['shortlisted', 'offered'])->get();
        $structures = SalaryStructure::where('tenant_id', $tenantId)->where('is_active', true)->get();
        return view('admin.salary.create-offer', compact('employees', 'candidates', 'structures'));
    }

    public function previewOffer(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $request->validate([
            'salary_structure_id' => 'required|exists:salary_structures,id',
            'ctc_annual'          => 'required|numeric|min:0',
            'position'            => 'required|string',
            'department'          => 'required|string',
            'joining_date'        => 'required|date',
            'offer_expiry'        => 'required|date|after:today',
            'employment_type'     => 'required|string',
        ]);
        $structure = SalaryStructure::findOrFail($request->salary_structure_id);
        $service   = new SalaryStructureService();
        $preview   = $service->compute($structure, (float)$request->ctc_annual);
        $tenant    = \App\Models\Tenant::find(session('hrms_tenant_id'));
        $employee  = $request->employee_id ? Employee::find($request->employee_id) : null;
        $candidate = $request->candidate_id ? Candidate::find($request->candidate_id) : null;
        return view('admin.salary.preview-offer', compact('structure', 'preview', 'request', 'tenant', 'employee', 'candidate'));
    }

    public function storeOffer(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $tenantId = session('hrms_tenant_id');
        $validated = $request->validate([
            'salary_structure_id' => 'required|exists:salary_structures,id',
            'ctc_annual'          => 'required|numeric|min:0',
            'position'            => 'required|string|max:255',
            'department'          => 'required|string|max:100',
            'joining_date'        => 'required|date',
            'offer_expiry'        => 'required|date',
            'employment_type'     => 'required|string',
            'work_location'       => 'nullable|string|max:255',
            'custom_clauses'      => 'nullable|string',
            'employee_id'         => 'nullable|exists:employees,id',
            'candidate_id'        => 'nullable|exists:candidates,id',
        ]);

        $offerNumber = 'OFR-' . strtoupper(substr(session('hrms_tenant_name'), 0, 3)) . '-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $offer = OfferLetter::create(array_merge($validated, [
            'tenant_id'   => $tenantId,
            'offer_number'=> $offerNumber,
            'status'      => 'draft',
            'created_by'  => session('hrms_user_id'),
        ]));

        return redirect()->route('admin.salary.offer.view', $offer->id)
            ->with('success', 'Offer letter created. Review and send.');
    }

    public function viewOffer($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $offer = OfferLetter::with(['employee', 'candidate', 'tenant'])
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);
        $structure = SalaryStructure::find($offer->salary_structure_id);
        $service   = new SalaryStructureService();
        $preview   = $structure ? $service->compute($structure, (float)$offer->ctc_annual) : null;
        return view('admin.salary.view-offer', compact('offer', 'preview'));
    }

    public function sendOffer($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $offer = OfferLetter::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $offer->update(['status' => 'sent', 'sent_at' => now()]);
        return redirect()->back()->with('success', 'Offer letter marked as sent.');
    }

    public function updateOfferStatus(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $offer = OfferLetter::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,expired']);
        $offer->update([
            'status'       => $request->status,
            'responded_at' => in_array($request->status, ['accepted', 'rejected']) ? now() : null,
        ]);
        return redirect()->back()->with('success', 'Offer status updated.');
    }
}
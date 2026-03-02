@extends('layouts.adminlte')
@section('title', 'TDS Calculation Result')
@section('page-title', 'TDS Calculation Result')
@section('breadcrumb', 'Result')
@section('content')
<div class="row">
  <div class="col-md-7">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>TDS Computation – {{ $employee->full_name }} – FY {{ $fy }}</h3>
      </div>
      <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
          <span><strong>Tax Regime:</strong></span>
          <span class="badge badge-{{ $result['tax_regime'] === 'new' ? 'primary' : 'success' }} badge-lg px-3 py-2">
            {{ strtoupper($result['tax_regime']) }} REGIME
          </span>
        </div>

        <h6 class="font-weight-bold text-primary">Income Computation</h6>
        <table class="table table-sm table-bordered mb-3">
          <tr><td>Gross Annual CTC</td><td class="text-right">₹{{ number_format($result['gross_annual_income'], 2) }}</td></tr>
          <tr class="table-success"><td>(-) Standard Deduction</td><td class="text-right text-success">-₹{{ number_format($result['standard_deduction'], 2) }}</td></tr>
          @if($result['total_exemptions'] > 0)
          <tr class="table-success"><td>(-) Total Exemptions (HRA, LTA)</td><td class="text-right text-success">-₹{{ number_format($result['total_exemptions'], 2) }}</td></tr>
          @endif
          @if($result['total_deductions'] > 0)
          <tr class="table-success"><td>(-) Chapter VI-A Deductions</td><td class="text-right text-success">-₹{{ number_format($result['total_deductions'], 2) }}</td></tr>
          @endif
          <tr class="table-warning"><td><strong>Taxable Income</strong></td><td class="text-right"><strong>₹{{ number_format($result['taxable_income'], 2) }}</strong></td></tr>
        </table>

        @if($result['total_deductions'] > 0)
        <h6 class="font-weight-bold text-success">Deduction Breakdown</h6>
        <table class="table table-sm table-bordered mb-3">
          @if($result['sec80c_applied'] > 0)<tr><td>Section 80C (PF/LIC/ELSS)</td><td class="text-right">₹{{ number_format($result['sec80c_applied'], 2) }}</td></tr>@endif
          @if($result['sec80d_applied'] > 0)<tr><td>Section 80D (Health Insurance)</td><td class="text-right">₹{{ number_format($result['sec80d_applied'], 2) }}</td></tr>@endif
          @if($result['sec24b_applied'] > 0)<tr><td>Section 24B (Home Loan Interest)</td><td class="text-right">₹{{ number_format($result['sec24b_applied'], 2) }}</td></tr>@endif
          @if($result['hra_exemption_computed'] > 0)<tr><td>HRA Exemption (Computed)</td><td class="text-right">₹{{ number_format($result['hra_exemption_computed'], 2) }}</td></tr>@endif
        </table>
        @endif

        <h6 class="font-weight-bold text-danger">Tax Computation</h6>
        <table class="table table-sm table-bordered mb-3">
          <tr><td>Income Tax (Slab Rate)</td><td class="text-right">₹{{ number_format($result['annual_tax_before_rebate'], 2) }}</td></tr>
          @if($result['rebate_87a'] > 0)
          <tr class="table-success"><td>(-) Rebate u/s 87A</td><td class="text-right text-success">-₹{{ number_format($result['rebate_87a'], 2) }}</td></tr>
          @endif
          <tr><td>Tax After Rebate</td><td class="text-right">₹{{ number_format($result['annual_tax'], 2) }}</td></tr>
          @if($result['surcharge'] > 0)
          <tr><td>Surcharge</td><td class="text-right">₹{{ number_format($result['surcharge'], 2) }}</td></tr>
          @endif
          <tr><td>Health &amp; Education Cess (4%)</td><td class="text-right">₹{{ number_format($result['health_education_cess'], 2) }}</td></tr>
          <tr class="table-danger"><td><strong>Total Annual Tax Liability</strong></td><td class="text-right"><strong>₹{{ number_format($result['total_tax_liability'], 2) }}</strong></td></tr>
          <tr style="background:linear-gradient(135deg,#dc3545,#c82333);color:white">
            <td><strong>Monthly TDS to Deduct</strong></td>
            <td class="text-right"><strong style="font-size:1.3rem">₹{{ number_format($result['monthly_tds'], 2) }}</strong></td>
          </tr>
        </table>

        <h6 class="font-weight-bold text-warning">Statutory Deductions (Monthly)</h6>
        <table class="table table-sm table-bordered mb-3">
          <tr><td>PF – Employee Contribution (12% of Basic)</td><td class="text-right">₹{{ number_format($result['pf_employee_monthly'], 2) }}</td></tr>
          <tr><td>PF – Employer Contribution</td><td class="text-right text-muted">₹{{ number_format($result['pf_employer_monthly'], 2) }}</td></tr>
          @if($result['esi_employee_monthly'] > 0)
          <tr><td>ESI – Employee (0.75%)</td><td class="text-right">₹{{ number_format($result['esi_employee_monthly'], 2) }}</td></tr>
          <tr><td>ESI – Employer (3.25%)</td><td class="text-right text-muted">₹{{ number_format($result['esi_employer_monthly'], 2) }}</td></tr>
          @endif
          <tr><td>Professional Tax</td><td class="text-right">₹{{ number_format($result['professional_tax_monthly'], 2) }}</td></tr>
          <tr class="table-warning"><td><strong>Total Monthly Statutory Deductions</strong></td><td class="text-right"><strong>₹{{ number_format($result['total_monthly_statutory'], 2) }}</strong></td></tr>
        </table>

      </div>
      <div class="card-footer">
        <form action="{{ route('admin.tds.declare.save', $employee->id) }}" method="POST" class="d-inline">
          @csrf
          <input type="hidden" name="financial_year" value="{{ $fy }}">
          <input type="hidden" name="tax_regime" value="{{ $request->tax_regime }}">
          <input type="hidden" name="is_senior_citizen" value="{{ $request->is_senior_citizen ?? 0 }}">
          <input type="hidden" name="section_80c" value="{{ $request->section_80c ?? 0 }}">
          <input type="hidden" name="section_80ccd1b" value="{{ $request->section_80ccd1b ?? 0 }}">
          <input type="hidden" name="section_80d" value="{{ $request->section_80d ?? 0 }}">
          <input type="hidden" name="section_80e" value="{{ $request->section_80e ?? 0 }}">
          <input type="hidden" name="section_24b" value="{{ $request->section_24b ?? 0 }}">
          <input type="hidden" name="section_80g" value="{{ $request->section_80g ?? 0 }}">
          <input type="hidden" name="section_80tta" value="{{ $request->section_80tta ?? 0 }}">
          <input type="hidden" name="hra_actual" value="{{ $request->hra_actual ?? 0 }}">
          <input type="hidden" name="rent_paid_annual" value="{{ $request->rent_paid_annual ?? 0 }}">
          <input type="hidden" name="metro_city" value="{{ $request->metro_city ?? 0 }}">
          <input type="hidden" name="lta_exemption" value="{{ $request->lta_exemption ?? 0 }}">
          <input type="hidden" name="other_deductions" value="{{ $request->other_deductions ?? 0 }}">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i>Save TDS Declaration
          </button>
        </form>
        <a href="{{ route('admin.tds.calculator') }}" class="btn btn-default ml-2">Recalculate</a>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title"><i class="fas fa-user mr-2"></i>Employee Summary</h3></div>
      <div class="card-body">
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Name</td><td><strong>{{ $employee->full_name }}</strong></td></tr>
          <tr><td class="text-muted">Department</td><td>{{ $employee->department }}</td></tr>
          <tr><td class="text-muted">Annual CTC</td><td>₹{{ number_format($employee->salary, 2) }}</td></tr>
          <tr><td class="text-muted">Monthly Gross</td><td>₹{{ number_format($employee->salary / 12, 2) }}</td></tr>
          <tr><td class="text-muted">Effective Monthly</td><td class="text-success">₹{{ number_format(($employee->salary / 12) - $result['total_monthly_statutory'], 2) }}</td></tr>
        </table>
        <div class="progress-group mt-3">
          @php $tdsPercent = $employee->salary > 0 ? round(($result['total_tax_liability'] / $employee->salary) * 100, 1) : 0; @endphp
          <span class="progress-text">Effective Tax Rate</span>
          <span class="float-right"><b>{{ $tdsPercent }}%</b></span>
          <div class="progress" style="height:10px">
            <div class="progress-bar bg-danger" style="width:{{ min($tdsPercent, 100) }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

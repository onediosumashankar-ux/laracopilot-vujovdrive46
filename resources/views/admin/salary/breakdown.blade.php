@extends('layouts.adminlte')
@section('title','Salary Breakdown')
@section('page-title','Employee Salary Breakdown')
@section('breadcrumb','Breakdown')
@push('styles')
<style>
@media print{.main-header,.main-sidebar,.content-header,.main-footer,.no-print{display:none!important}.content-wrapper{margin:0!important;background:#fff!important}}
</style>
@endpush
@section('content')
<div class="no-print mb-3">
  <button onclick="window.print()" class="btn btn-default"><i class="fas fa-print mr-1"></i>Print</button>
  <a href="{{ route('admin.salary.assign') }}" class="btn btn-default ml-2">Back</a>
  <a href="{{ route('admin.salary.offer.create') }}" class="btn btn-success ml-2">
    <i class="fas fa-file-contract mr-1"></i>Generate Offer Letter
  </a>
</div>

<div style="max-width:850px;margin:0 auto">
  <div class="card">
    <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-0">Salary Breakdown – {{ $ess->employee->full_name }}</h4>
          <small style="opacity:.8">{{ $ess->salaryStructure->name }} &bull; Effective: {{ $ess->effective_from->format('d M Y') }}</small>
        </div>
        <div class="text-right">
          <div style="font-size:1.5rem;font-weight:700">₹{{ number_format($preview['ctc_annual'],0) }}</div>
          <small style="opacity:.8">Annual CTC</small>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <table class="table table-sm table-borderless">
            <tr><td class="text-muted">Employee</td><td><strong>{{ $ess->employee->full_name }}</strong></td></tr>
            <tr><td class="text-muted">Department</td><td>{{ $ess->employee->department }}</td></tr>
            <tr><td class="text-muted">Position</td><td>{{ $ess->employee->position }}</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-sm table-borderless">
            <tr><td class="text-muted">Structure</td><td><strong>{{ $ess->salaryStructure->name }}</strong></td></tr>
            <tr><td class="text-muted">Effective</td><td>{{ $ess->effective_from->format('d M Y') }}</td></tr>
            <tr><td class="text-muted">CTC Override</td><td>{{ $ess->ctc_override ? '₹'.number_format($ess->ctc_override,0) : 'Default' }}</td></tr>
          </table>
        </div>
      </div>

      <h6 class="text-success font-weight-bold border-bottom pb-1"><i class="fas fa-plus-circle mr-1"></i>Earnings</h6>
      <table class="table table-sm table-bordered mb-3">
        <thead class="thead-light"><tr><th>Component</th><th>Code</th><th>Basis</th><th class="text-right">Monthly</th><th class="text-right">Annual</th><th>Taxable</th></tr></thead>
        <tbody>
          @foreach(collect($preview['components'])->where('type','earning') as $r)
          <tr>
            <td>{{ $r['name'] }}</td>
            <td><code>{{ $r['code'] }}</code></td>
            <td><small>{{ ucfirst(str_replace('_',' ',$r['calculation_type'])) }} {{ $r['calculation_type'] === 'fixed' ? '' : '('.$r['value'].'%)' }}</small></td>
            <td class="text-right text-success">₹{{ number_format($r['monthly_amount'],2) }}</td>
            <td class="text-right text-success">₹{{ number_format($r['annual_amount'],2) }}</td>
            <td>{{ $r['taxable'] ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-secondary">No</span>' }}</td>
          </tr>
          @endforeach
          <tr class="table-success"><td colspan="3"><strong>Total Gross</strong></td><td class="text-right"><strong>₹{{ number_format($preview['gross_monthly'],2) }}</strong></td><td class="text-right"><strong>₹{{ number_format($preview['gross_annual'],2) }}</strong></td><td></td></tr>
        </tbody>
      </table>

      @if(collect($preview['components'])->where('type','deduction')->isNotEmpty())
      <h6 class="text-danger font-weight-bold border-bottom pb-1"><i class="fas fa-minus-circle mr-1"></i>Deductions</h6>
      <table class="table table-sm table-bordered mb-3">
        <thead class="thead-light"><tr><th>Component</th><th>Code</th><th>Basis</th><th class="text-right">Monthly</th><th class="text-right">Annual</th><th></th></tr></thead>
        <tbody>
          @foreach(collect($preview['components'])->where('type','deduction') as $r)
          <tr>
            <td>{{ $r['name'] }}</td><td><code>{{ $r['code'] }}</code></td>
            <td><small>{{ ucfirst(str_replace('_',' ',$r['calculation_type'])) }}</small></td>
            <td class="text-right text-danger">-₹{{ number_format($r['monthly_amount'],2) }}</td>
            <td class="text-right text-danger">-₹{{ number_format($r['annual_amount'],2) }}</td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif

      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr><td>Gross Monthly</td><td class="text-right">₹{{ number_format($preview['gross_monthly'],2) }}</td></tr>
            <tr><td>Deductions Monthly</td><td class="text-right text-danger">-₹{{ number_format($preview['total_deductions_monthly'],2) }}</td></tr>
            <tr class="table-success"><td><strong>Net Take-Home</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_monthly'],2) }}</strong></td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-bordered">
            <tr><td>Annual CTC</td><td class="text-right">₹{{ number_format($preview['ctc_annual'],2) }}</td></tr>
            <tr><td>Taxable Income</td><td class="text-right">₹{{ number_format($preview['taxable_annual'],2) }}</td></tr>
            <tr><td>Employer Contributions</td><td class="text-right">₹{{ number_format($preview['employer_contributions_annual'],2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

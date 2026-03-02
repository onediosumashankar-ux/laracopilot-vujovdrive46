@extends('layouts.adminlte')
@section('title','Preview Assignment')
@section('page-title','Preview Salary Assignment')
@section('breadcrumb','Preview')
@section('content')
<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-1"></i>Employee</h3></div>
      <div class="card-body">
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Name</td><td><strong>{{ $employee->full_name }}</strong></td></tr>
          <tr><td class="text-muted">Department</td><td>{{ $employee->department }}</td></tr>
          <tr><td class="text-muted">Position</td><td>{{ $employee->position }}</td></tr>
          <tr><td class="text-muted">Structure</td><td>{{ $structure->name }}</td></tr>
          <tr><td class="text-muted">Effective CTC</td><td><strong>₹{{ number_format($request->ctc_override ?: $structure->ctc_amount, 0) }}</strong></td></tr>
        </table>
      </div>
    </div>
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title">Summary</h3></div>
      <div class="card-body p-2">
        <table class="table table-sm mb-0">
          <tr><td>Annual CTC</td><td class="text-right font-weight-bold">₹{{ number_format($preview['ctc_annual'],0) }}</td></tr>
          <tr><td>Monthly Gross</td><td class="text-right text-success">₹{{ number_format($preview['gross_monthly'],0) }}</td></tr>
          <tr><td>Monthly Deductions</td><td class="text-right text-danger">-₹{{ number_format($preview['total_deductions_monthly'],0) }}</td></tr>
          <tr class="table-success"><td><strong>Net Monthly</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_monthly'],0) }}</strong></td></tr>
          <tr><td>Taxable Annual</td><td class="text-right">₹{{ number_format($preview['taxable_annual'],0) }}</td></tr>
        </table>
      </div>
    </div>
    <form action="{{ route('admin.salary.assign.save') }}" method="POST">
      @csrf
      <input type="hidden" name="employee_id" value="{{ $employee->id }}">
      <input type="hidden" name="salary_structure_id" value="{{ $structure->id }}">
      <input type="hidden" name="ctc_override" value="{{ $request->ctc_override }}">
      <div class="form-group">
        <label class="font-weight-bold">Effective From *</label>
        <input type="date" name="effective_from" class="form-control" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="form-group">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Salary revision notes..."></textarea>
      </div>
      <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save mr-1"></i>Confirm & Save Assignment</button>
      <a href="{{ route('admin.salary.assign') }}" class="btn btn-default btn-block mt-1">Back</a>
    </form>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-table mr-2"></i>Full Salary Breakdown</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
          <thead class="thead-dark"><tr><th>Component</th><th>Type</th><th>Calc</th><th class="text-right">Monthly</th><th class="text-right">Annual</th></tr></thead>
          <tbody>
            @foreach($preview['components'] as $r)
            <tr>
              <td><strong>{{ $r['name'] }}</strong><br><small><code>{{ $r['code'] }}</code></small></td>
              <td><span class="badge badge-{{ $r['type'] === 'earning' ? 'success' : ($r['type'] === 'deduction' ? 'danger' : 'info') }}">{{ ucfirst(str_replace('_',' ',$r['type'])) }}</span></td>
              <td><small>{{ ucfirst(str_replace('_',' ',$r['calculation_type'])) }}</small></td>
              <td class="text-right {{ $r['type'] === 'earning' ? 'text-success' : 'text-danger' }}">
                {{ $r['type'] === 'deduction' ? '-' : '' }}₹{{ number_format($r['monthly_amount'],2) }}
              </td>
              <td class="text-right {{ $r['type'] === 'earning' ? 'text-success' : 'text-danger' }}">
                {{ $r['type'] === 'deduction' ? '-' : '' }}₹{{ number_format($r['annual_amount'],2) }}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="table-dark">
            <tr><td colspan="3">Gross</td><td class="text-right">₹{{ number_format($preview['gross_monthly'],2) }}</td><td class="text-right">₹{{ number_format($preview['gross_annual'],2) }}</td></tr>
            <tr><td colspan="3" class="text-danger">Total Deductions</td><td class="text-right text-danger">-₹{{ number_format($preview['total_deductions_monthly'],2) }}</td><td class="text-right text-danger">-₹{{ number_format($preview['total_deductions_annual'],2) }}</td></tr>
            <tr><td colspan="3"><strong>Net Pay</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_monthly'],2) }}</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_annual'],2) }}</strong></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

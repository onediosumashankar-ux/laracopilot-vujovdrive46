@extends('layouts.adminlte')
@section('title', 'TDS Management')
@section('page-title', 'TDS Management')
@section('breadcrumb', 'TDS')
@section('content')
<div class="row">
  <div class="col-md-3 col-6">
    <div class="small-box bg-danger"><div class="inner"><h3>₹{{ number_format($totalTdsLiability, 0) }}</h3><p>Annual TDS Liability</p></div><div class="icon"><i class="fas fa-rupee-sign"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-warning"><div class="inner"><h3>₹{{ number_format($totalMonthlyTds, 0) }}</h3><p>Monthly TDS (Total)</p></div><div class="icon"><i class="fas fa-calendar-alt"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-success"><div class="inner"><h3>{{ $employeesWithDeclaration }} / {{ $totalEmployees }}</h3><p>Declarations Filed</p></div><div class="icon"><i class="fas fa-file-alt"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-info"><div class="inner"><h3>{{ $fy }}</h3><p>Financial Year</p></div><div class="icon"><i class="fas fa-chart-bar"></i></div></div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-3">
    <a href="{{ route('admin.tds.calculator') }}" class="btn btn-primary mr-2"><i class="fas fa-calculator mr-1"></i>TDS Calculator</a>
    <a href="{{ route('admin.tds.report') }}" class="btn btn-info mr-2"><i class="fas fa-chart-bar mr-1"></i>Annual Report</a>
    <a href="{{ route('admin.tds.deductions') }}" class="btn btn-warning"><i class="fas fa-list mr-1"></i>Monthly Deductions</a>
  </div>
</div>

<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Employee TDS Declarations – FY {{ $fy }}</h3>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr>
          <th>Employee</th><th>Regime</th>
          <th>Gross Annual</th><th>Exemptions</th><th>Deductions (80C etc)</th>
          <th>Taxable Income</th><th>Annual Tax</th><th>Cess</th>
          <th>Total Liability</th><th>Monthly TDS</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($declarations as $decl)
        <tr>
          <td><strong>{{ $decl->employee->full_name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $decl->employee->department ?? '' }}</small></td>
          <td>
            <span class="badge badge-{{ $decl->tax_regime === 'new' ? 'primary' : 'success' }}">
              {{ strtoupper($decl->tax_regime) }} REGIME
            </span>
          </td>
          <td>₹{{ number_format($decl->gross_annual_income, 0) }}</td>
          <td class="text-success">-₹{{ number_format($decl->total_exemptions, 0) }}</td>
          <td class="text-success">-₹{{ number_format($decl->total_deductions, 0) }}</td>
          <td><strong>₹{{ number_format($decl->taxable_income, 0) }}</strong></td>
          <td>₹{{ number_format($decl->annual_tax, 0) }}</td>
          <td class="text-warning">₹{{ number_format($decl->health_education_cess, 0) }}</td>
          <td><strong class="text-danger">₹{{ number_format($decl->total_tax_liability, 0) }}</strong></td>
          <td><strong class="text-danger">₹{{ number_format($decl->monthly_tds, 0) }}/mo</strong></td>
          <td>
            <a href="{{ route('admin.tds.declare', $decl->employee_id) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
            <a href="{{ route('admin.tds.certificate', $decl->employee_id) }}" class="btn btn-xs btn-info" title="Form 16"><i class="fas fa-certificate"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center py-5 text-muted">
          <i class="fas fa-file-alt fa-2x d-block mb-2"></i>No TDS declarations yet.<br>
          <a href="{{ route('admin.tds.calculator') }}" class="btn btn-primary mt-2">Start TDS Calculator</a>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $declarations->links() }}</div>
</div>
@endsection

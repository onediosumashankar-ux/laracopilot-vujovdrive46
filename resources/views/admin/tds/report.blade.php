@extends('layouts.adminlte')
@section('title', 'TDS Annual Report')
@section('page-title', 'TDS Annual Report')
@section('breadcrumb', 'TDS Report')
@section('content')
<div class="row mb-3">
  <div class="col-md-4">
    <form method="GET" class="form-inline">
      <label class="mr-2">Financial Year:</label>
      <select name="fy" class="form-control mr-2">
        @foreach(['2024-25','2023-24','2022-23'] as $fyOpt)
        <option value="{{ $fyOpt }}" {{ $fyOpt === $fy ? 'selected' : '' }}>FY {{ $fyOpt }}</option>
        @endforeach
      </select>
      <button class="btn btn-primary">View</button>
    </form>
  </div>
  <div class="col-md-8 text-right">
    <button onclick="window.print()" class="btn btn-default"><i class="fas fa-print mr-1"></i>Print</button>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-rupee-sign"></i></span>
      <div class="info-box-content"><span class="info-box-text">Total Annual TDS</span>
        <span class="info-box-number">₹{{ number_format($declarations->sum('total_tax_liability'), 0) }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-users"></i></span>
      <div class="info-box-content"><span class="info-box-text">Employees Declared</span>
        <span class="info-box-number">{{ $declarations->count() }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-calendar"></i></span>
      <div class="info-box-content"><span class="info-box-text">Total Deducted This FY</span>
        <span class="info-box-number">₹{{ number_format($deductions->sum('total_tds'), 0) }}</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-table mr-2"></i>Employee-wise TDS – FY {{ $fy }}</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
          <thead class="thead-dark">
            <tr><th>Employee</th><th>Regime</th><th>Taxable Income</th><th>Annual Tax</th><th>Monthly TDS</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($declarations as $d)
            <tr>
              <td><strong>{{ $d->employee->full_name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $d->employee->department ?? '' }}</small></td>
              <td><span class="badge badge-{{ $d->tax_regime === 'new' ? 'primary' : 'success' }}">{{ strtoupper($d->tax_regime) }}</span></td>
              <td>₹{{ number_format($d->taxable_income, 0) }}</td>
              <td>₹{{ number_format($d->total_tax_liability, 0) }}</td>
              <td class="text-danger font-weight-bold">₹{{ number_format($d->monthly_tds, 0) }}</td>
              <td>
                <a href="{{ route('admin.tds.certificate', $d->employee_id) }}" class="btn btn-xs btn-info"><i class="fas fa-certificate"></i> Form 16</a>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">No declarations for this FY.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Month-wise TDS</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>Month</th><th class="text-right">TDS</th></tr></thead>
          <tbody>
            @php $mNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December']; @endphp
            @foreach($monthlyTotals as $m => $total)
            <tr>
              <td>{{ $mNames[$m] ?? $m }}</td>
              <td class="text-right font-weight-bold text-danger">₹{{ number_format($total, 0) }}</td>
            </tr>
            @endforeach
            @if($monthlyTotals->count())
            <tr class="table-dark"><td><strong>Total</strong></td><td class="text-right"><strong>₹{{ number_format($monthlyTotals->sum(), 0) }}</strong></td></tr>
            @else
            <tr><td colspan="2" class="text-center text-muted">No deductions recorded.</td></tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

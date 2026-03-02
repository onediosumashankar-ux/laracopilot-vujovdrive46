@extends('layouts.adminlte')
@section('title', 'Payroll Details')
@section('page-title', 'Payroll Record Details')
@section('breadcrumb', 'Details')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Employee Info</h3></div>
      <div class="card-body">
        <div class="text-center mb-3">
          <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#2d6a9f,#4db8ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:24px;margin:0 auto">
            {{ strtoupper(substr($payroll->employee->first_name ?? 'E', 0, 1)) }}
          </div>
          <h5 class="mt-2 mb-0">{{ $payroll->employee->full_name }}</h5>
          <small class="text-muted">{{ $payroll->employee->position }} &bull; {{ $payroll->employee->department }}</small>
        </div>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Period</td><td>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('F Y') }}</td></tr>
          <tr><td class="text-muted">Status</td><td><span class="badge badge-{{ $payroll->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($payroll->status) }}</span></td></tr>
          @if($payroll->paid_at)<tr><td class="text-muted">Paid On</td><td>{{ $payroll->paid_at->format('d M Y') }}</td></tr>@endif
          <tr><td class="text-muted">Per-Day Rate</td><td>${{ number_format($payroll->per_day_salary, 2) }}</td></tr>
        </table>
      </div>
      <div class="card-footer">
        <a href="{{ route('admin.payroll.payslip', $payroll->id) }}" class="btn btn-primary btn-block"><i class="fas fa-file-pdf mr-1"></i>View Payslip</a>
        @if($payroll->status === 'pending')
        <form action="{{ route('admin.payroll.process', $payroll->id) }}" method="POST" class="mt-2">
          @csrf
          <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i>Mark as Paid</button>
        </form>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Salary Breakdown</h3>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          @php $stats = [['label'=>'Working Days','val'=>$payroll->working_days,'color'=>'secondary'],['label'=>'Present','val'=>$payroll->present_days,'color'=>'success'],['label'=>'Absent','val'=>$payroll->absent_days,'color'=>'danger'],['label'=>'Late','val'=>$payroll->late_days,'color'=>'warning'],['label'=>'Holidays','val'=>$payroll->holiday_days,'color'=>'info'],['label'=>'Leaves','val'=>$payroll->leave_days,'color'=>'primary']]; @endphp
          @foreach($stats as $s)
          <div class="col-2 text-center">
            <div style="background:#f8f9fa;border-radius:8px;padding:8px 4px">
              <div style="font-size:1.4rem;font-weight:700" class="text-{{ $s['color'] }}">{{ $s['val'] }}</div>
              <div class="small text-muted">{{ $s['label'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
        <table class="table table-bordered">
          <tr class="table-light"><th colspan="2">EARNINGS</th></tr>
          <tr><td>Basic Salary</td><td class="text-right">${{ number_format($payroll->basic_salary, 2) }}</td></tr>
          @if($payroll->allowances > 0)<tr><td>Allowances</td><td class="text-right">${{ number_format($payroll->allowances, 2) }}</td></tr>@endif
          @if($payroll->bonus > 0)<tr><td>Bonus</td><td class="text-right">${{ number_format($payroll->bonus, 2) }}</td></tr>@endif
          @if($payroll->overtime_pay > 0)<tr><td>Overtime</td><td class="text-right">${{ number_format($payroll->overtime_pay, 2) }}</td></tr>@endif
          <tr class="table-success"><td><strong>Gross Salary</strong></td><td class="text-right"><strong>${{ number_format($payroll->gross_salary, 2) }}</strong></td></tr>
          <tr class="table-light"><th colspan="2">DEDUCTIONS</th></tr>
          <tr><td>Absence Deduction ({{ $payroll->absent_days }} days)</td><td class="text-right text-danger">-${{ number_format($payroll->absence_deduction, 2) }}</td></tr>
          <tr><td>Late Deduction ({{ $payroll->late_days }} times)</td><td class="text-right text-warning">-${{ number_format($payroll->late_deduction, 2) }}</td></tr>
          <tr><td>Other Deductions</td><td class="text-right text-danger">-${{ number_format(max(0, $payroll->deductions - $payroll->absence_deduction - $payroll->late_deduction), 2) }}</td></tr>
          <tr><td>Income Tax</td><td class="text-right text-warning">-${{ number_format($payroll->tax, 2) }}</td></tr>
          <tr class="table-danger"><td><strong>Total Deductions</strong></td><td class="text-right"><strong>-${{ number_format($payroll->deductions + $payroll->tax, 2) }}</strong></td></tr>
          <tr style="background:linear-gradient(135deg,#1a3a5c,#2d6a9f);color:white"><td><strong>NET PAY</strong></td><td class="text-right"><strong style="font-size:1.3rem">${{ number_format($payroll->net_salary, 2) }}</strong></td></tr>
        </table>
        @if($payroll->notes)<div class="alert alert-light border"><i class="fas fa-sticky-note mr-1"></i>{{ $payroll->notes }}</div>@endif
      </div>
    </div>
  </div>
</div>
@endsection

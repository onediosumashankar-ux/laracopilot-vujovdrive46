@extends('layouts.adminlte')
@section('title', 'Payslip')
@section('page-title', 'Payslip')
@section('breadcrumb', 'Payslip')
@push('styles')
<style>
  @media print {
    .main-header, .main-sidebar, .content-header, .main-footer, .no-print { display: none !important; }
    .content-wrapper { margin: 0 !important; background: white !important; }
    .payslip-container { box-shadow: none !important; }
  }
  .payslip-container { max-width: 820px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
  .ps-header { background: linear-gradient(135deg, #1a2942 0%, #2d6a9f 100%); color: white; padding: 30px 40px; }
  .ps-body { padding: 30px 40px; }
  .ps-table th { background: #f8f9fa; font-weight: 600; }
  .ps-footer { background: #1a2942; color: #aaa; text-align: center; padding: 15px; font-size: 0.8rem; }
  .net-box { background: linear-gradient(135deg, #1a3a5c, #2d6a9f); color: white; border-radius: 10px; padding: 20px; text-align: center; }
</style>
@endpush
@section('content')
<div class="no-print mb-3">
  <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i>Print Payslip</button>
  <a href="{{ route('admin.payroll.index') }}" class="btn btn-default ml-2">Back to List</a>
  @if($payroll->status === 'pending')
  <form action="{{ route('admin.payroll.process', $payroll->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-success ml-2"><i class="fas fa-check mr-1"></i>Mark as Paid</button>
  </form>
  @endif
</div>

<div class="payslip-container">
  <!-- Header -->
  <div class="ps-header">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h2 class="mb-0 font-weight-bold">{{ $payroll->employee->tenant->name ?? 'Company Name' }}</h2>
        <div class="mt-1" style="opacity:0.8">{{ $payroll->employee->tenant->address ?? '' }}</div>
        <div style="opacity:0.8">{{ $payroll->employee->tenant->email ?? '' }}</div>
      </div>
      <div class="text-right">
        <h4 class="mb-0">PAYSLIP</h4>
        <div style="opacity:0.8">{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('F Y') }}</div>
        <span class="badge badge-{{ $payroll->status === 'paid' ? 'success' : 'warning' }} mt-1">{{ strtoupper($payroll->status) }}</span>
      </div>
    </div>
  </div>

  <div class="ps-body">
    <!-- Employee Info -->
    <div class="row mb-4">
      <div class="col-md-6">
        <h6 class="text-muted text-uppercase small">Employee Details</h6>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted pl-0" style="width:40%">Name</td><td><strong>{{ $payroll->employee->full_name }}</strong></td></tr>
          <tr><td class="text-muted pl-0">Department</td><td>{{ $payroll->employee->department }}</td></tr>
          <tr><td class="text-muted pl-0">Position</td><td>{{ $payroll->employee->position }}</td></tr>
          <tr><td class="text-muted pl-0">Emp. Type</td><td>{{ str_replace('_',' ',ucfirst($payroll->employee->employment_type)) }}</td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6 class="text-muted text-uppercase small">Payment Details</h6>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted pl-0" style="width:40%">Pay Period</td><td>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('d M Y') }}</td></tr>
          <tr><td class="text-muted pl-0">Bank Account</td><td>{{ $payroll->employee->bank_account ?? 'N/A' }}</td></tr>
          <tr><td class="text-muted pl-0">Tax ID</td><td>{{ $payroll->employee->tax_id ?? 'N/A' }}</td></tr>
          @if($payroll->paid_at)<tr><td class="text-muted pl-0">Paid On</td><td>{{ $payroll->paid_at->format('d M Y') }}</td></tr>@endif
        </table>
      </div>
    </div>

    <!-- Attendance Summary -->
    <h6 class="text-muted text-uppercase small mb-2">Attendance Summary</h6>
    <div class="row mb-4">
      @php
        $stats = [
          ['label'=>'Working Days','val'=>$payroll->working_days,'color'=>'secondary'],
          ['label'=>'Present','val'=>$payroll->present_days,'color'=>'success'],
          ['label'=>'Absent','val'=>$payroll->absent_days,'color'=>'danger'],
          ['label'=>'Late','val'=>$payroll->late_days,'color'=>'warning'],
          ['label'=>'Holidays','val'=>$payroll->holiday_days,'color'=>'info'],
          ['label'=>'Leaves','val'=>$payroll->leave_days,'color'=>'primary'],
        ];
      @endphp
      @foreach($stats as $s)
      <div class="col-2 text-center">
        <div style="background:#f8f9fa;border-radius:8px;padding:10px 5px">
          <div style="font-size:1.5rem;font-weight:700" class="text-{{ $s['color'] }}">{{ $s['val'] }}</div>
          <div class="small text-muted">{{ $s['label'] }}</div>
        </div>
      </div>
      @endforeach
    </div>

    <!-- Salary Breakdown -->
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-success text-uppercase small mb-2"><i class="fas fa-plus-circle mr-1"></i>Earnings</h6>
        <table class="table table-sm table-bordered ps-table">
          <tr><td>Basic Salary</td><td class="text-right">${{ number_format($payroll->basic_salary, 2) }}</td></tr>
          @if($payroll->allowances > 0)<tr><td>Allowances</td><td class="text-right">${{ number_format($payroll->allowances, 2) }}</td></tr>@endif
          @if($payroll->bonus > 0)<tr><td>Bonus</td><td class="text-right">${{ number_format($payroll->bonus, 2) }}</td></tr>@endif
          @if($payroll->overtime_pay > 0)<tr><td>Overtime</td><td class="text-right">${{ number_format($payroll->overtime_pay, 2) }}</td></tr>@endif
          <tr class="table-success"><td><strong>Gross Pay</strong></td><td class="text-right"><strong>${{ number_format($payroll->gross_salary, 2) }}</strong></td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6 class="text-danger text-uppercase small mb-2"><i class="fas fa-minus-circle mr-1"></i>Deductions</h6>
        <table class="table table-sm table-bordered ps-table">
          @if($payroll->absence_deduction > 0)<tr><td>Absence ({{ $payroll->absent_days }}d)</td><td class="text-right text-danger">-${{ number_format($payroll->absence_deduction, 2) }}</td></tr>@endif
          @if($payroll->late_deduction > 0)<tr><td>Late Arrival ({{ $payroll->late_days }}×)</td><td class="text-right text-warning">-${{ number_format($payroll->late_deduction, 2) }}</td></tr>@endif
          @if(($payroll->deductions - $payroll->absence_deduction - $payroll->late_deduction) > 0.01)<tr><td>Other Deductions</td><td class="text-right text-danger">-${{ number_format($payroll->deductions - $payroll->absence_deduction - $payroll->late_deduction, 2) }}</td></tr>@endif
          <tr><td>Income Tax</td><td class="text-right text-warning">-${{ number_format($payroll->tax, 2) }}</td></tr>
          <tr class="table-danger"><td><strong>Total Deductions</strong></td><td class="text-right"><strong>-${{ number_format($payroll->deductions + $payroll->tax, 2) }}</strong></td></tr>
        </table>
      </div>
    </div>

    <!-- Net Pay -->
    <div class="net-box mt-3">
      <div class="text-light small text-uppercase letter-spacing-1">Net Take-Home Pay</div>
      <div style="font-size:2.8rem;font-weight:800">${{ number_format($payroll->net_salary, 2) }}</div>
      <div class="text-light small">{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('F Y') }} &bull; Per-Day Rate: ${{ number_format($payroll->per_day_salary, 2) }}</div>
    </div>

    @if($payroll->notes)
    <div class="mt-3 p-2" style="background:#f8f9fa;border-radius:6px;font-size:0.85rem">
      <i class="fas fa-sticky-note mr-1 text-muted"></i>{{ $payroll->notes }}
    </div>
    @endif
  </div>

  <div class="ps-footer">
    This is a computer-generated payslip and does not require a signature. &copy; {{ date('Y') }} {{ $payroll->employee->tenant->name ?? 'Company' }} &bull; Powered by TalentFlow HRMS
  </div>
</div>
@endsection

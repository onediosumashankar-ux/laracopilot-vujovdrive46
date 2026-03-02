@extends('layouts.adminlte')
@section('title', 'Payroll')
@section('page-title', 'Payroll Management')
@section('breadcrumb', 'Payroll')
@section('content')

<!-- Bulk Generate Card -->
<div class="card card-warning card-outline">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-magic mr-2"></i>Generate Monthly Payroll</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
    </div>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.payroll.bulk-generate') }}" method="POST" class="form-inline">
      @csrf
      <div class="form-group mr-3">
        <label class="mr-2 font-weight-bold">Month:</label>
        <select name="month" class="form-control">
          @foreach(['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $num => $name)
          <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group mr-3">
        <label class="mr-2 font-weight-bold">Year:</label>
        <select name="year" class="form-control">
          @for($y = now()->year; $y >= now()->year - 3; $y--)
          <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>
      <button type="submit" class="btn btn-warning mr-3" onclick="return confirm('Generate payroll for ALL active employees? Existing records for this period will be skipped.')">
        <i class="fas fa-users mr-1"></i>Bulk Generate All
      </button>
      <a href="{{ route('admin.payroll.create') }}" class="btn btn-primary mr-3">
        <i class="fas fa-user mr-1"></i>Single Employee
      </a>
      <a href="{{ route('admin.payroll.holidays') }}" class="btn btn-default">
        <i class="fas fa-calendar-alt mr-1"></i>Holiday Calendar
      </a>
    </form>
    <div class="mt-3 p-3" style="background:#fff8e1;border-radius:8px;border-left:4px solid #ffc107">
      <strong><i class="fas fa-info-circle mr-1 text-warning"></i>How payslip calculation works:</strong>
      <ul class="mb-0 mt-1 small">
        <li><strong>Working Days</strong> = Total weekdays in month</li>
        <li><strong>Effective Days</strong> = Working Days &minus; Public Holidays</li>
        <li><strong>Per-Day Salary</strong> = Monthly Salary &divide; Effective Days</li>
        <li><strong>Absence Deduction</strong> = Absent Days &times; Per-Day Salary</li>
        <li><strong>Late Deduction</strong> = Late Days &times; 0.5 Day Salary</li>
        <li><strong>Unpaid Leave Deduction</strong> = Unpaid Leave Days &times; Per-Day Salary</li>
        <li><strong>Paid Leaves &amp; Holidays</strong> = No deduction (full pay)</li>
        <li><strong>Tax</strong> = Progressive bracket calculation on annualized salary</li>
      </ul>
    </div>
  </div>
</div>

<!-- KPI Row -->
<div class="row">
  <div class="col-md-3 col-6">
    <div class="small-box bg-success"><div class="inner"><h3>${{ number_format($totalPaid, 0) }}</h3><p>Total Paid Out</p></div><div class="icon"><i class="fas fa-dollar-sign"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-warning"><div class="inner"><h3>{{ $pendingPayroll }}</h3><p>Pending Approval</p></div><div class="icon"><i class="fas fa-clock"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-info"><div class="inner"><h3>{{ $payrolls->total() }}</h3><p>Total Records</p></div><div class="icon"><i class="fas fa-file-invoice-dollar"></i></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="small-box bg-primary"><div class="inner"><h3>{{ now()->format('M Y') }}</h3><p>Current Period</p></div><div class="icon"><i class="fas fa-calendar"></i></div></div>
  </div>
</div>

<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Payroll Records</h3>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr>
          <th>Employee</th><th>Period</th>
          <th>Days <small>(Work/Present/Absent)</small></th>
          <th>Holidays</th><th>Leaves</th>
          <th>Gross</th><th>Deductions</th><th>Tax</th><th>Net Pay</th>
          <th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payrolls as $p)
        <tr>
          <td><strong>{{ $p->employee->full_name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $p->employee->department ?? '' }}</small></td>
          <td>{{ \Carbon\Carbon::parse($p->pay_period_start)->format('M Y') }}</td>
          <td>
            <span class="badge badge-secondary" title="Working Days">{{ $p->working_days }}W</span>
            <span class="badge badge-success" title="Present Days">{{ $p->present_days }}P</span>
            <span class="badge badge-danger" title="Absent Days">{{ $p->absent_days }}A</span>
            @if($p->late_days > 0)<span class="badge badge-warning" title="Late Days">{{ $p->late_days }}L</span>@endif
          </td>
          <td><span class="badge badge-info">{{ $p->holiday_days }}</span></td>
          <td><span class="badge badge-primary">{{ $p->leave_days }}</span></td>
          <td>${{ number_format($p->gross_salary, 2) }}</td>
          <td class="text-danger">-${{ number_format($p->deductions, 2) }}</td>
          <td class="text-warning">-${{ number_format($p->tax, 2) }}</td>
          <td><strong class="text-success">${{ number_format($p->net_salary, 2) }}</strong></td>
          <td>
            <span class="badge badge-{{ $p->status === 'paid' ? 'success' : ($p->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($p->status) }}</span>
          </td>
          <td>
            <a href="{{ route('admin.payroll.show', $p->id) }}" class="btn btn-xs btn-info" title="Details"><i class="fas fa-eye"></i></a>
            <a href="{{ route('admin.payroll.payslip', $p->id) }}" class="btn btn-xs btn-secondary" title="Payslip"><i class="fas fa-file-pdf"></i></a>
            @if($p->status === 'pending')
            <form action="{{ route('admin.payroll.process', $p->id) }}" method="POST" class="d-inline">
              @csrf
              <button class="btn btn-xs btn-success" onclick="return confirm('Mark as Paid?')" title="Pay"><i class="fas fa-check"></i></button>
            </form>
            <form action="{{ route('admin.payroll.destroy', $p->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center text-muted py-5"><i class="fas fa-file-invoice-dollar fa-2x d-block mb-2"></i>No payroll records. Use Bulk Generate above.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $payrolls->links() }}</div>
</div>
@endsection

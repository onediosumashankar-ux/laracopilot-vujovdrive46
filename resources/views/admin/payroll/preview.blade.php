@extends('layouts.adminlte')
@section('title', 'Payslip Preview')
@section('page-title', 'Payslip Calculation Preview')
@section('breadcrumb', 'Preview')
@section('content')

<div class="row">
  <!-- LEFT: Attendance & Leave Summary -->
  <div class="col-md-5">
    <div class="card card-primary card-outline">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Attendance Summary</h3></div>
      <div class="card-body p-0">
        <table class="table table-bordered mb-0">
          <tr><th>Total Working Days</th><td class="text-right"><strong>{{ $calculation['working_days'] }}</strong></td></tr>
          <tr class="table-info"><th>Public Holidays</th><td class="text-right"><span class="badge badge-info">{{ $calculation['holiday_days'] }}</span></td></tr>
          <tr><th>Effective Working Days</th><td class="text-right"><strong>{{ $calculation['effective_working_days'] }}</strong></td></tr>
          <tr class="table-success"><th>Days Present</th><td class="text-right"><span class="badge badge-success">{{ $calculation['present_days'] }}</span></td></tr>
          <tr class="table-warning"><th>Half Days</th><td class="text-right"><span class="badge badge-warning">{{ $calculation['half_days'] }}</span></td></tr>
          <tr class="table-danger"><th>Days Absent</th><td class="text-right"><span class="badge badge-danger">{{ $calculation['absent_days'] }}</span></td></tr>
          <tr class="table-warning"><th>Late Arrivals</th><td class="text-right"><span class="badge badge-warning">{{ $calculation['late_days'] }}</span></td></tr>
          <tr><th>Leave Days (Total)</th><td class="text-right">{{ $calculation['leave_days'] }}</td></tr>
          <tr><th>&nbsp;&nbsp;↳ Paid Leaves</th><td class="text-right text-success">{{ $calculation['paid_leave_days'] }}</td></tr>
          <tr><th>&nbsp;&nbsp;↳ Unpaid Leaves</th><td class="text-right text-danger">{{ $calculation['unpaid_leave_days'] }}</td></tr>
        </table>
      </div>
    </div>

    @if($calculation['holidays']->count())
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-umbrella-beach mr-2 text-info"></i>Holidays This Month</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          @foreach($calculation['holidays'] as $h)
          <tr>
            <td>{{ $h->date->format('d M') }}</td>
            <td><strong>{{ $h->name }}</strong></td>
            <td><span class="badge badge-{{ $h->type === 'public' ? 'info' : 'secondary' }}">{{ ucfirst($h->type) }}</span></td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    @endif

    @if($calculation['approved_leaves']->count())
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-minus mr-2 text-warning"></i>Approved Leaves</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          @foreach($calculation['approved_leaves'] as $lv)
          <tr>
            <td>{{ $lv->start_date->format('d M') }} - {{ $lv->end_date->format('d M') }}</td>
            <td>{{ $lv->leaveType->name ?? 'Leave' }}</td>
            <td><span class="badge badge-{{ $lv->leaveType && $lv->leaveType->paid ? 'success' : 'danger' }}">{{ $lv->leaveType && $lv->leaveType->paid ? 'Paid' : 'Unpaid' }}</span></td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>
    @endif
  </div>

  <!-- RIGHT: Salary Calculation -->
  <div class="col-md-7">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title">
          <i class="fas fa-file-invoice-dollar mr-2"></i>
          {{ $employee->full_name }} &mdash; {{ \Carbon\Carbon::create(null, $request->month)->format('F') }} {{ $request->year }}
        </h3>
      </div>
      <div class="card-body">
        <!-- Earnings -->
        <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-plus-circle mr-1"></i>Earnings</h6>
        <table class="table table-bordered table-sm mb-3">
          <tr><td>Basic Monthly Salary</td><td class="text-right">${{ number_format($calculation['basic_salary'], 2) }}</td></tr>
          <tr><td>Allowances</td><td class="text-right">${{ number_format($calculation['allowances'], 2) }}</td></tr>
          <tr><td>Bonus</td><td class="text-right">${{ number_format($calculation['bonus'], 2) }}</td></tr>
          <tr><td>Overtime Pay</td><td class="text-right">${{ number_format($calculation['overtime_pay'], 2) }}</td></tr>
          <tr class="table-success"><td><strong>Gross Salary</strong></td><td class="text-right"><strong>${{ number_format($calculation['gross_salary'], 2) }}</strong></td></tr>
        </table>

        <!-- Deductions -->
        <h6 class="font-weight-bold text-danger mb-2"><i class="fas fa-minus-circle mr-1"></i>Deductions</h6>
        <table class="table table-bordered table-sm mb-3">
          <tr><td>Per-Day Rate</td><td class="text-right">${{ number_format($calculation['per_day_salary'], 2) }}/day</td></tr>
          <tr class="table-danger"><td>Absence Deduction ({{ $calculation['absent_days'] }} days)</td><td class="text-right text-danger">-${{ number_format($calculation['absence_deduction'], 2) }}</td></tr>
          <tr class="table-warning"><td>Late Arrival Deduction ({{ $calculation['late_days'] }} &times; 0.5 day)</td><td class="text-right text-warning">-${{ number_format($calculation['late_deduction'], 2) }}</td></tr>
          <tr class="table-warning"><td>Half-Day Deduction ({{ $calculation['half_days'] }} &times; 0.5 day)</td><td class="text-right text-warning">-${{ number_format($calculation['half_day_deduction'] ?? 0, 2) }}</td></tr>
          <tr class="table-danger"><td>Unpaid Leave Deduction ({{ $calculation['unpaid_leave_days'] }} days)</td><td class="text-right text-danger">-${{ number_format($calculation['unpaid_leave_deduction'], 2) }}</td></tr>
          <tr><td><strong>Total Deductions</strong></td><td class="text-right text-danger"><strong>-${{ number_format($calculation['deductions'], 2) }}</strong></td></tr>
          <tr><td>Income Tax (Progressive Bracket)</td><td class="text-right text-warning">-${{ number_format($calculation['tax'], 2) }}</td></tr>
        </table>

        <!-- Net Pay -->
        <div class="p-3 text-center" style="background:linear-gradient(135deg,#1a3a5c,#2d6a9f);border-radius:10px;color:#fff">
          <div class="text-light small">NET TAKE-HOME PAY</div>
          <div style="font-size:2.5rem;font-weight:700">${{ number_format($calculation['net_salary'], 2) }}</div>
          <div class="text-light small">{{ \Carbon\Carbon::create(null, $request->month)->format('F') }} {{ $request->year }}</div>
        </div>
      </div>

      <!-- Save Form -->
      <div class="card-footer">
        <form action="{{ route('admin.payroll.store') }}" method="POST">
          @csrf
          <input type="hidden" name="employee_id" value="{{ $employee->id }}">
          <input type="hidden" name="pay_period_start" value="{{ $calculation['pay_period_start'] }}">
          <input type="hidden" name="pay_period_end" value="{{ $calculation['pay_period_end'] }}">
          <input type="hidden" name="working_days" value="{{ $calculation['working_days'] }}">
          <input type="hidden" name="present_days" value="{{ $calculation['present_days'] }}">
          <input type="hidden" name="absent_days" value="{{ $calculation['absent_days'] }}">
          <input type="hidden" name="late_days" value="{{ $calculation['late_days'] }}">
          <input type="hidden" name="holiday_days" value="{{ $calculation['holiday_days'] }}">
          <input type="hidden" name="leave_days" value="{{ $calculation['leave_days'] }}">
          <input type="hidden" name="half_days" value="{{ $calculation['half_days'] }}">
          <input type="hidden" name="per_day_salary" value="{{ $calculation['per_day_salary'] }}">
          <input type="hidden" name="basic_salary" value="{{ $calculation['basic_salary'] }}">
          <input type="hidden" name="allowances" value="{{ $calculation['allowances'] }}">
          <input type="hidden" name="bonus" value="{{ $calculation['bonus'] }}">
          <input type="hidden" name="overtime_pay" value="{{ $calculation['overtime_pay'] }}">
          <input type="hidden" name="gross_salary" value="{{ $calculation['gross_salary'] }}">
          <input type="hidden" name="absence_deduction" value="{{ $calculation['absence_deduction'] }}">
          <input type="hidden" name="late_deduction" value="{{ $calculation['late_deduction'] }}">
          <input type="hidden" name="deductions" value="{{ $calculation['deductions'] }}">
          <input type="hidden" name="tax" value="{{ $calculation['tax'] }}">
          <input type="hidden" name="net_salary" value="{{ $calculation['net_salary'] }}">
          <div class="form-group">
            <label>Additional Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes...">Absent: {{ $calculation['absent_days'] }}d | Late: {{ $calculation['late_days'] }}d | Leaves: {{ $calculation['leave_days'] }}d | Holidays: {{ $calculation['holiday_days'] }}d</textarea>
          </div>
          <button type="submit" class="btn btn-success btn-block">
            <i class="fas fa-save mr-1"></i>Confirm & Save Payroll
          </button>
          <a href="{{ route('admin.payroll.create') }}" class="btn btn-default btn-block mt-1">Recalculate</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

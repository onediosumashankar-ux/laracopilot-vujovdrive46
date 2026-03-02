@extends('layouts.adminlte')
@section('title', 'Employee Profile')
@section('page-title', $employee->full_name)
@section('breadcrumb', 'Profile')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-body text-center" style="background:linear-gradient(135deg,#f8f9fa,#e9ecef);border-radius:10px 10px 0 0">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#2d6a9f,#4db8ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:32px;margin:0 auto 15px">
          {{ strtoupper(substr($employee->first_name, 0, 1)) }}
        </div>
        <h3 class="profile-username">{{ $employee->full_name }}</h3>
        <p class="text-muted">{{ $employee->position }} &bull; {{ $employee->department }}</p>
        <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'danger' }} badge-lg">{{ ucfirst($employee->status) }}</span>
      </div>
      <div class="card-footer p-0">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="{{ route('admin.employees.edit', $employee->id) }}"><i class="fas fa-edit text-warning mr-2"></i>Edit Profile</a></li>
        </ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3 class="card-title">Contact Information</h3></div>
      <div class="card-body">
        <div class="mb-2"><i class="fas fa-envelope mr-2 text-muted"></i>{{ $employee->email }}</div>
        <div class="mb-2"><i class="fas fa-phone mr-2 text-muted"></i>{{ $employee->phone ?? 'Not provided' }}</div>
        <div class="mb-2"><i class="fas fa-map-marker-alt mr-2 text-muted"></i>{{ $employee->address ?? 'Not provided' }}</div>
        <div class="mb-2"><i class="fas fa-exclamation-circle mr-2 text-danger"></i>{{ $employee->emergency_contact ?? 'Not provided' }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <ul class="nav nav-pills" id="profileTabs">
          <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#employment">Employment</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#payroll">Payroll</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#attendance">Attendance</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#leaves">Leaves</a></li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <div class="tab-pane fade show active" id="employment">
            <div class="row">
              <div class="col-sm-6"><div class="description-block border-right"><h5 class="description-header">${{ number_format($employee->salary, 0) }}</h5><span class="description-text">Annual Salary</span></div></div>
              <div class="col-sm-6"><div class="description-block"><h5 class="description-header">{{ $employee->hire_date->format('M d, Y') }}</h5><span class="description-text">Hire Date</span></div></div>
            </div>
            <table class="table table-bordered mt-3">
              <tr><th style="width:40%">Department</th><td>{{ $employee->department }}</td></tr>
              <tr><th>Position</th><td>{{ $employee->position }}</td></tr>
              <tr><th>Employment Type</th><td>{{ str_replace('_', ' ', ucfirst($employee->employment_type)) }}</td></tr>
              <tr><th>Gender</th><td>{{ ucfirst($employee->gender) }}</td></tr>
              <tr><th>Date of Birth</th><td>{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</td></tr>
              <tr><th>Tax ID</th><td>{{ $employee->tax_id ?? 'N/A' }}</td></tr>
              <tr><th>Bank Account</th><td>{{ $employee->bank_account ?? 'N/A' }}</td></tr>
            </table>
          </div>
          <div class="tab-pane fade" id="payroll">
            <table class="table table-hover">
              <thead class="thead-light"><tr><th>Period</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($employee->payrolls as $pay)
                <tr>
                  <td>{{ $pay->pay_period_start->format('M Y') }}</td>
                  <td>${{ number_format($pay->gross_salary, 2) }}</td>
                  <td>${{ number_format($pay->deductions + $pay->tax, 2) }}</td>
                  <td><strong>${{ number_format($pay->net_salary, 2) }}</strong></td>
                  <td><span class="badge badge-{{ $pay->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($pay->status) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="attendance">
            <table class="table table-hover">
              <thead class="thead-light"><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($employee->attendances()->orderBy('check_in','desc')->take(10)->get() as $att)
                <tr>
                  <td>{{ $att->check_in->format('M d, Y') }}</td>
                  <td>{{ $att->check_in->format('h:i A') }} @if($att->is_late)<span class="badge badge-warning ml-1">Late</span>@endif</td>
                  <td>{{ $att->check_out ? $att->check_out->format('h:i A') : '<span class="badge badge-info">In Office</span>' }}</td>
                  <td>{{ $att->hours_worked ?? '-' }}h</td>
                  <td><span class="badge badge-success">{{ ucfirst($att->status) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="leaves">
            <table class="table table-hover">
              <thead class="thead-light"><tr><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($employee->leaveRequests()->with('leaveType')->orderBy('created_at','desc')->take(10)->get() as $leave)
                <tr>
                  <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
                  <td>{{ $leave->start_date->format('M d, Y') }}</td>
                  <td>{{ $leave->end_date->format('M d, Y') }}</td>
                  <td>{{ $leave->days }}</td>
                  <td><span class="badge badge-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($leave->status) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

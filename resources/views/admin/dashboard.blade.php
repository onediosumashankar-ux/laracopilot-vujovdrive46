@extends('layouts.adminlte')
@section('title', 'Admin Dashboard')
@section('page-title', 'HR Dashboard')
@section('breadcrumb', 'Dashboard')
@section('content')
<!-- KPI Cards -->
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner"><h3>{{ $totalEmployees }}</h3><p>Total Employees</p></div>
      <div class="icon"><i class="fas fa-users"></i></div>
      <a href="{{ route('admin.employees.index') }}" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner"><h3>{{ $activeEmployees }}</h3><p>Active Employees</p></div>
      <div class="icon"><i class="fas fa-user-check"></i></div>
      <a href="{{ route('admin.employees.index') }}" class="small-box-footer">Details <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner"><h3>{{ $openJobs }}</h3><p>Open Job Positions</p></div>
      <div class="icon"><i class="fas fa-briefcase"></i></div>
      <a href="{{ route('admin.recruitment.index') }}" class="small-box-footer">Recruitment <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner"><h3>{{ $pendingLeaves }}</h3><p>Pending Leave Requests</p></div>
      <div class="icon"><i class="fas fa-calendar-times"></i></div>
      <a href="{{ route('admin.leaves.index') }}" class="small-box-footer">Review <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-clock"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Today Present</span>
        <span class="info-box-number">{{ $todayAttendance }} / {{ $totalEmployees }}</span>
        <div class="progress"><div class="progress-bar bg-primary" style="width: {{ $attendanceRate }}%"></div></div>
        <span class="progress-description">{{ $attendanceRate }}% attendance rate</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">This Month Payroll</span>
        <span class="info-box-number">${{ number_format($thisMonthPayroll, 0) }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-plus"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Departments</span>
        <span class="info-box-number">{{ $departmentStats->count() }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-chart-bar"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Workforce</span>
        <span class="info-box-number">{{ $activeEmployees }} Active</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-7">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Pending Leave Requests</h3>
        <div class="card-tools"><a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-light">View All</a></div>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="thead-light"><tr><th>Employee</th><th>Type</th><th>Duration</th><th>Action</th></tr></thead>
          <tbody>
            @forelse($recentLeaves as $leave)
            <tr>
              <td>{{ $leave->employee->full_name ?? 'N/A' }}</td>
              <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
              <td>{{ $leave->days }} day(s)<br><small class="text-muted">{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</small></td>
              <td>
                <form action="{{ route('admin.leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                  @csrf @method('PUT')
                  <button class="btn btn-xs btn-success"><i class="fas fa-check"></i></button>
                </form>
                <form action="{{ route('admin.leaves.reject', $leave->id) }}" method="POST" class="d-inline">
                  @csrf @method('PUT')
                  <button class="btn btn-xs btn-danger"><i class="fas fa-times"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No pending leave requests.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>By Department</h3>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @foreach($departmentStats as $dept)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="fas fa-briefcase mr-2 text-primary"></i>{{ $dept->department }}</span>
            <span class="badge badge-primary badge-pill">{{ $dept->count }}</span>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Recent Employees</h3>
        <div class="card-tools"><a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success"><i class="fas fa-plus mr-1"></i>Add Employee</a></div>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="thead-light"><tr><th>Name</th><th>Department</th><th>Position</th><th>Hire Date</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($recentEmployees as $emp)
            <tr>
              <td><a href="{{ route('admin.employees.show', $emp->id) }}">{{ $emp->full_name }}</a><br><small class="text-muted">{{ $emp->email }}</small></td>
              <td>{{ $emp->department }}</td>
              <td>{{ $emp->position }}</td>
              <td>{{ $emp->hire_date->format('M d, Y') }}</td>
              <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($emp->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No employees yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

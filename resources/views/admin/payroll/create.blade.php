@extends('layouts.adminlte')
@section('title', 'Generate Payslip')
@section('page-title', 'Generate Payslip')
@section('breadcrumb', 'Create Payroll')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Calculate Employee Payslip</h3>
      </div>
      <form action="{{ route('admin.payroll.preview') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label class="font-weight-bold">Employee *</label>
            <select name="employee_id" class="form-control" required>
              <option value="">-- Select Employee --</option>
              @foreach($employees as $emp)
              <option value="{{ $emp->id }}">{{ $emp->full_name }} &mdash; {{ $emp->department }} &mdash; ${{ number_format($emp->salary/12, 0) }}/mo</option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Month *</label>
              <select name="month" class="form-control" required>
                @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Year *</label>
              <select name="year" class="form-control" required>
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-1"></i>
            The system will automatically pull <strong>attendance records</strong>, <strong>approved leaves</strong>, and <strong>public holidays</strong> for the selected period to calculate accurate deductions.
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-calculator mr-1"></i>Preview Payslip Calculation
          </button>
          <a href="{{ route('admin.payroll.index') }}" class="btn btn-default btn-block mt-1">Back to List</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

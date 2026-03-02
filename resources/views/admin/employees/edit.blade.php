@extends('layouts.adminlte')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('breadcrumb', 'Edit')
@section('content')
<form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
  @csrf @method('PUT')
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
          <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Edit: {{ $employee->full_name }}</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group"><label>First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" required></div>
            <div class="col-md-6 form-group"><label>Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" required></div>
            <div class="col-md-6 form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}"></div>
            <div class="col-md-6 form-group">
              <label>Gender *</label>
              <select name="gender" class="form-control" required>
                <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $employee->gender) === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-6 form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}"></div>
            <div class="col-md-12 form-group"><label>Address</label><textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea></div>
            <div class="col-md-12 form-group"><label>Emergency Contact</label><input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $employee->emergency_contact) }}"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
          <h3 class="card-title"><i class="fas fa-briefcase mr-2"></i>Employment</h3>
        </div>
        <div class="card-body">
          <div class="form-group"><label>Department *</label><input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}" required></div>
          <div class="form-group"><label>Position *</label><input type="text" name="position" class="form-control" value="{{ old('position', $employee->position) }}" required></div>
          <div class="form-group">
            <label>Employment Type *</label>
            <select name="employment_type" class="form-control" required>
              <option value="full_time" {{ old('employment_type', $employee->employment_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
              <option value="part_time" {{ old('employment_type', $employee->employment_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
              <option value="contract" {{ old('employment_type', $employee->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
              <option value="intern" {{ old('employment_type', $employee->employment_type) === 'intern' ? 'selected' : '' }}>Intern</option>
            </select>
          </div>
          <div class="form-group"><label>Hire Date *</label><input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required></div>
          <div class="form-group"><label>Annual Salary *</label><input type="number" name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" required></div>
          <div class="form-group">
            <label>Status *</label>
            <select name="status" class="form-control" required>
              <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
              <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
          </div>
          <div class="form-group"><label>Bank Account</label><input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $employee->bank_account) }}"></div>
          <div class="form-group"><label>Tax ID</label><input type="text" name="tax_id" class="form-control" value="{{ old('tax_id', $employee->tax_id) }}"></div>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Employee</button>
  <a href="{{ route('admin.employees.index') }}" class="btn btn-default ml-2">Cancel</a>
</form>
@endsection

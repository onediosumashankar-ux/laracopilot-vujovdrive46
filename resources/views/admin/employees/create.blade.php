@extends('layouts.adminlte')
@section('title', 'Add Employee')
@section('page-title', 'Add New Employee')
@section('breadcrumb', 'Create')
@section('content')
<form action="{{ route('admin.employees.store') }}" method="POST">
  @csrf
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
          <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Personal Information</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>First Name *</label>
              <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
              @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Last Name *</label>
              <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
              @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Email Address *</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Phone Number</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6 form-group">
              <label>Gender *</label>
              <select name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Date of Birth</label>
              <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            </div>
            <div class="col-md-12 form-group">
              <label>Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="col-md-12 form-group">
              <label>Emergency Contact</label>
              <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}" placeholder="Name & Phone Number">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
          <h3 class="card-title"><i class="fas fa-briefcase mr-2"></i>Employment Details</h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Department *</label>
            <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}" list="dept-list" required>
            <datalist id="dept-list">
              <option value="Engineering"><option value="Product"><option value="Sales"><option value="Marketing">
              <option value="HR"><option value="Finance"><option value="Operations"><option value="Design">
            </datalist>
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Position *</label>
            <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" required>
            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Employment Type *</label>
            <select name="employment_type" class="form-control" required>
              <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
              <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
              <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
              <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
            </select>
          </div>
          <div class="form-group">
            <label>Hire Date *</label>
            <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date') }}" required>
          </div>
          <div class="form-group">
            <label>Annual Salary (USD) *</label>
            <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" min="0" required>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select name="status" class="form-control" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <hr>
          <h6 class="font-weight-bold"><i class="fas fa-lock mr-1 text-warning"></i>Account</h6>
          <div class="form-group">
            <label>Bank Account</label>
            <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
          </div>
          <div class="form-group">
            <label>Tax ID</label>
            <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id') }}">
          </div>
          <div class="form-group">
            <label>Portal Password *</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Create Employee</button>
      <a href="{{ route('admin.employees.index') }}" class="btn btn-default ml-2">Cancel</a>
    </div>
  </div>
</form>
@endsection

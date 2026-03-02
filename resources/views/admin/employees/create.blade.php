@extends('layouts.adminlte')
@section('title','Add Employee')
@section('page-title','Add New Employee')
@section('breadcrumb','Add Employee')
@section('content')
<div class="row">
  <div class="col-md-9">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>New Employee Registration</h3>
      </div>
      <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        <div class="card-body">

          <!-- Branch Selection – PROMINENT -->
          <div class="alert alert-info p-2 mb-3">
            <i class="fas fa-code-branch mr-1"></i>
            <strong>Select Branch First</strong> – The employee will be assigned to this branch/office location.
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold text-primary"><i class="fas fa-code-branch mr-1"></i>Branch / Office Location *</label>
              <select name="branch_id" class="form-control form-control-lg @error('branch_id') is-invalid @enderror" required>
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                  {{ $branch->is_head_office ? '⭐ ' : '' }}{{ $branch->name }} – {{ $branch->city }}
                </option>
                @endforeach
              </select>
              @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Employment Type *</label>
              <select name="employment_type" class="form-control form-control-lg" required>
                <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
              </select>
            </div>
          </div>

          <hr>
          <h6 class="font-weight-bold text-muted">Personal Information</h6>
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">First Name *</label>
              <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
              @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Last Name *</label>
              <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
              @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Gender</label>
              <select name="gender" class="form-control">
                <option value="">-- Select --</option>
                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-5 form-group">
              <label class="font-weight-bold">Email *</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91-XXXXX-XXXXX">
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Date of Birth</label>
              <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            </div>
          </div>

          <hr>
          <h6 class="font-weight-bold text-muted">Job Details</h6>
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Department *</label>
              <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}" placeholder="Engineering" list="dept-list" required>
              <datalist id="dept-list">
                @foreach(['Engineering','Human Resources','Finance','Operations','Marketing','Sales','Legal','Product','Design','Customer Success'] as $d)
                <option value="{{ $d }}">@endforeach
              </datalist>
              @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Position / Designation *</label>
              <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" placeholder="Senior Software Engineer" required>
              @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Reporting Manager</label>
              <input type="text" name="manager" class="form-control" value="{{ old('manager') }}" placeholder="Manager name">
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Date of Joining *</label>
              <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Annual CTC (₹) *</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary') }}" required>
              </div>
              @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Status</label>
              <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <hr>
          <h6 class="font-weight-bold text-muted">Financial & Compliance</h6>
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">PAN Number</label>
              <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id') }}" placeholder="ABCDE1234F" style="text-transform:uppercase" maxlength="10">
            </div>
            <div class="col-md-8 form-group">
              <label class="font-weight-bold">Bank Account (IFSC | Account No.)</label>
              <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}" placeholder="SBIN0001234 | 38901234567">
            </div>
            <div class="col-md-12 form-group">
              <label class="font-weight-bold">Residential Address</label>
              <textarea name="address" class="form-control" rows="2" placeholder="Full address for official records">{{ old('address') }}</textarea>
            </div>
          </div>

        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save mr-1"></i>Add Employee</button>
          <a href="{{ route('admin.employees.index') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title"><i class="fas fa-info-circle mr-1"></i>Available Branches</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @foreach($branches as $b)
          <li class="list-group-item py-2">
            <strong>{{ $b->name }}</strong>
            @if($b->is_head_office)<span class="badge badge-warning ml-1">HQ</span>@endif
            <br><small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $b->city }}, {{ $b->state }}</small>
          </li>
          @endforeach
        </ul>
      </div>
      <div class="card-footer">
        <a href="{{ route('admin.branches.create') }}" class="btn btn-sm btn-block btn-outline-primary">
          <i class="fas fa-plus mr-1"></i>Add New Branch
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

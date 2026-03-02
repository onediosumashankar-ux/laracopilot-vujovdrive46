@extends('layouts.adminlte')
@section('title','Add Branch')
@section('page-title','Add New Branch')
@section('breadcrumb','Add Branch')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-code-branch mr-2"></i>New Branch / Office Location</h3>
      </div>
      <form action="{{ route('admin.branches.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-8 form-group">
              <label class="font-weight-bold">Branch Name * <small class="text-muted">(e.g. Mumbai – Andheri East)</small></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="City – Area Name" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Branch Code</label>
              <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="MUM-AND" style="text-transform:uppercase">
            </div>
            <div class="col-md-12 form-group">
              <label class="font-weight-bold">Street Address</label>
              <textarea name="address" class="form-control" rows="2" placeholder="Building, Floor, Road Name">{{ old('address') }}</textarea>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">City *</label>
              <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Mumbai" required>
              @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">State *</label>
              <select name="state" class="form-control @error('state') is-invalid @enderror" required>
                <option value="">-- Select State --</option>
                @foreach(['Andhra Pradesh','Assam','Bihar','Chhattisgarh','Delhi','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal'] as $state)
                <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
              </select>
              @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Pincode</label>
              <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" placeholder="400069" maxlength="6">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91-22-1234-5678">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Branch Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="branch@company.com">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Branch Manager</label>
              <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name') }}" placeholder="Manager full name">
            </div>
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-6 form-group">
                  <div class="icheck-success">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label for="is_active">Active Branch (employees can be assigned)</label>
                  </div>
                </div>
                @if(!$hasHeadOffice)
                <div class="col-md-6 form-group">
                  <div class="icheck-warning">
                    <input type="checkbox" name="is_head_office" id="is_head_office" value="1">
                    <label for="is_head_office"><i class="fas fa-star mr-1 text-warning"></i>Mark as Head Office</label>
                  </div>
                </div>
                @else
                <div class="col-md-6">
                  <div class="alert alert-info p-2 small"><i class="fas fa-info-circle mr-1"></i>A head office already exists. Edit the existing one to change it.</div>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Create Branch</button>
          <a href="{{ route('admin.branches.index') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@extends('layouts.adminlte')
@section('title', 'Add Tenant')
@section('page-title', 'Add New Tenant')
@section('breadcrumb', 'Create Tenant')
@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-building mr-2"></i>Tenant Information</h3>
      </div>
      <form action="{{ route('superadmin.tenants.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Company Name *</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Domain/Slug *</label>
              <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror" value="{{ old('domain') }}" placeholder="e.g. mycompany" required>
              @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Company Email *</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-12 form-group">
              <label>Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="col-md-6 form-group">
              <label>Plan *</label>
              <select name="plan" class="form-control" required>
                <option value="starter" {{ old('plan') === 'starter' ? 'selected' : '' }}>Starter (Up to 50 employees)</option>
                <option value="professional" {{ old('plan') === 'professional' ? 'selected' : '' }}>Professional (Up to 200 employees)</option>
                <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise (Unlimited)</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Status *</label>
              <select name="status" class="form-control" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Office Latitude (for Geofencing)</label>
              <input type="number" step="any" name="office_lat" class="form-control" value="{{ old('office_lat') }}" placeholder="e.g. 37.7749">
            </div>
            <div class="col-md-6 form-group">
              <label>Office Longitude (for Geofencing)</label>
              <input type="number" step="any" name="office_lng" class="form-control" value="{{ old('office_lng') }}" placeholder="e.g. -122.4194">
            </div>
          </div>
          <hr>
          <h5 class="font-weight-bold"><i class="fas fa-user-shield mr-2 text-primary"></i>Admin Account</h5>
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Admin Name *</label>
              <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" value="{{ old('admin_name') }}" required>
              @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Admin Email *</label>
              <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}" required>
              @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group">
              <label>Admin Password *</label>
              <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" required>
              @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Create Tenant</button>
          <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

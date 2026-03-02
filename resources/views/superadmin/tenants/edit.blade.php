@extends('layouts.adminlte')
@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')
@section('breadcrumb', 'Edit')
@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $tenant->name }}</h3>
      </div>
      <form action="{{ route('superadmin.tenants.update', $tenant->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Company Name *</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
            </div>
            <div class="col-md-6 form-group">
              <label>Company Email *</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}" required>
            </div>
            <div class="col-md-6 form-group">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}">
            </div>
            <div class="col-md-6 form-group">
              <label>Plan *</label>
              <select name="plan" class="form-control" required>
                <option value="starter" {{ old('plan', $tenant->plan) === 'starter' ? 'selected' : '' }}>Starter</option>
                <option value="professional" {{ old('plan', $tenant->plan) === 'professional' ? 'selected' : '' }}>Professional</option>
                <option value="enterprise" {{ old('plan', $tenant->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Status *</label>
              <select name="status" class="form-control" required>
                <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
              </select>
            </div>
            <div class="col-md-12 form-group">
              <label>Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $tenant->address) }}</textarea>
            </div>
            <div class="col-md-6 form-group">
              <label>Office Latitude</label>
              <input type="number" step="any" name="office_lat" class="form-control" value="{{ old('office_lat', $tenant->office_lat) }}">
            </div>
            <div class="col-md-6 form-group">
              <label>Office Longitude</label>
              <input type="number" step="any" name="office_lng" class="form-control" value="{{ old('office_lng', $tenant->office_lng) }}">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Tenant</button>
          <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

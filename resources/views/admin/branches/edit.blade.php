@extends('layouts.adminlte')
@section('title','Edit Branch')
@section('page-title','Edit Branch')
@section('breadcrumb','Edit')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#b8860b,#daa520);color:#fff">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $branch->name }}</h3>
      </div>
      <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-8 form-group">
              <label class="font-weight-bold">Branch Name *</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Code</label>
              <input type="text" name="code" class="form-control" value="{{ old('code', $branch->code) }}">
            </div>
            <div class="col-md-12 form-group">
              <label class="font-weight-bold">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $branch->address) }}</textarea>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">City *</label>
              <input type="text" name="city" class="form-control" value="{{ old('city', $branch->city) }}" required>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">State *</label>
              <select name="state" class="form-control" required>
                @foreach(['Andhra Pradesh','Assam','Bihar','Chhattisgarh','Delhi','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal'] as $state)
                <option value="{{ $state }}" {{ old('state', $branch->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Pincode</label>
              <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $branch->pincode) }}">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}">
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Manager</label>
              <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $branch->manager_name) }}">
            </div>
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-4">
                  <div class="icheck-success"><input type="checkbox" name="is_active" id="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }}><label for="is_active">Active</label></div>
                </div>
                <div class="col-md-4">
                  <div class="icheck-warning"><input type="checkbox" name="is_head_office" id="is_head_office" value="1" {{ $branch->is_head_office ? 'checked' : '' }}><label for="is_head_office"><i class="fas fa-star mr-1 text-warning"></i>Head Office</label></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Branch</button>
          <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

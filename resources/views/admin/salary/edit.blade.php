@extends('layouts.adminlte')
@section('title','Edit Structure')
@section('page-title','Edit Salary Structure')
@section('breadcrumb','Edit')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#b8860b,#daa520);color:#fff">
        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit: {{ $structure->name }}</h3>
      </div>
      <form action="{{ route('admin.salary.update', $structure->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="row">
            <div class="col-md-8 form-group"><label class="font-weight-bold">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $structure->name) }}" required></div>
            <div class="col-md-4 form-group"><label class="font-weight-bold">Code</label><input type="text" name="code" class="form-control" value="{{ old('code', $structure->code) }}"></div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">CTC Amount *</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                <input type="number" name="ctc_amount" class="form-control" value="{{ old('ctc_amount', $structure->ctc_amount) }}" required>
              </div>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Type *</label>
              <select name="type" class="form-control" required>
                <option value="annual" {{ old('type',$structure->type) === 'annual' ? 'selected' : '' }}>Annual CTC</option>
                <option value="monthly" {{ old('type',$structure->type) === 'monthly' ? 'selected' : '' }}>Monthly CTC</option>
              </select>
            </div>
            <div class="col-md-12 form-group"><label>Description</label><textarea name="description" class="form-control" rows="2">{{ old('description', $structure->description) }}</textarea></div>
            <div class="col-md-12 form-group">
              <div class="icheck-primary"><input type="checkbox" name="is_active" id="is_active" value="1" {{ $structure->is_active ? 'checked' : '' }}><label for="is_active">Active</label></div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Structure</button>
          <a href="{{ route('admin.salary.show', $structure->id) }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

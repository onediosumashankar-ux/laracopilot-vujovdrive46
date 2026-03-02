@extends('layouts.adminlte')
@section('title', $branch->name)
@section('page-title', $branch->name)
@section('breadcrumb','Branch Detail')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-{{ $branch->is_head_office ? 'warning' : 'primary' }} card-outline">
      <div class="card-body">
        @if($branch->is_head_office)
        <div class="text-center mb-2"><span class="badge badge-warning px-3 py-1"><i class="fas fa-star mr-1"></i>HEAD OFFICE</span></div>
        @endif
        <h5 class="font-weight-bold">{{ $branch->name }}</h5>
        @if($branch->code)<code class="d-block mb-2">{{ $branch->code }}</code>@endif
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted pl-0" style="width:35%">City</td><td>{{ $branch->city }}</td></tr>
          <tr><td class="text-muted pl-0">State</td><td>{{ $branch->state }}</td></tr>
          @if($branch->pincode)<tr><td class="text-muted pl-0">Pincode</td><td>{{ $branch->pincode }}</td></tr>@endif
          @if($branch->address)<tr><td class="text-muted pl-0">Address</td><td>{{ $branch->address }}</td></tr>@endif
          @if($branch->phone)<tr><td class="text-muted pl-0">Phone</td><td>{{ $branch->phone }}</td></tr>@endif
          @if($branch->email)<tr><td class="text-muted pl-0">Email</td><td>{{ $branch->email }}</td></tr>@endif
          @if($branch->manager_name)<tr><td class="text-muted pl-0">Manager</td><td>{{ $branch->manager_name }}</td></tr>@endif
          <tr><td class="text-muted pl-0">Status</td><td><span class="badge badge-{{ $branch->is_active ? 'success' : 'secondary' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
        </table>
        <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-warning btn-block"><i class="fas fa-edit mr-1"></i>Edit Branch</a>
      </div>
    </div>

    <!-- Department Stats -->
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i>By Department</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>Department</th><th class="text-right">Count</th></tr></thead>
          <tbody>
            @forelse($deptStats as $dept => $count)
            <tr><td>{{ $dept }}</td><td class="text-right font-weight-bold">{{ $count }}</td></tr>
            @empty
            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="row mb-3">
      <div class="col-6"><div class="info-box bg-success mb-0"><span class="info-box-icon"><i class="fas fa-user-check"></i></span><div class="info-box-content"><span class="info-box-text">Active</span><span class="info-box-number">{{ $activeCount }}</span></div></div></div>
      <div class="col-6"><div class="info-box bg-secondary mb-0"><span class="info-box-icon"><i class="fas fa-user-times"></i></span><div class="info-box-content"><span class="info-box-text">Inactive</span><span class="info-box-number">{{ $inactiveCount }}</span></div></div></div>
    </div>

    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Employees at this Branch</h3>
        <div class="card-tools">
          <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success">
            <i class="fas fa-user-plus mr-1"></i>Add Employee
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-dark">
            <tr><th>Name</th><th>Department</th><th>Position</th><th>Type</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($employees as $emp)
            <tr>
              <td>
                <strong>{{ $emp->full_name }}</strong><br>
                <small class="text-muted">{{ $emp->email }}</small>
              </td>
              <td>{{ $emp->department }}</td>
              <td>{{ $emp->position }}</td>
              <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$emp->employment_type)) }}</span></td>
              <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($emp->status) }}</span></td>
              <td>
                <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">
              <i class="fas fa-users fa-2x d-block mb-2"></i>No employees assigned to this branch yet.
              <a href="{{ route('admin.employees.create') }}">Add the first employee</a>
            </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reassign Employees -->
    @if($employees->count() > 0)
    <div class="card mt-3">
      <div class="card-header bg-warning"><h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Reassign Employees to Another Branch</h3></div>
      <form action="{{ route('admin.branches.reassign', $branch->id) }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-5 form-group">
              <label class="font-weight-bold">Select Employees</label>
              <select name="employee_ids[]" class="form-control" multiple style="height:120px">
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }} – {{ $emp->department }}</option>
                @endforeach
              </select>
              <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
            </div>
            <div class="col-md-5 form-group">
              <label class="font-weight-bold">Move to Branch</label>
              <select name="target_branch_id" class="form-control" required>
                <option value="">-- Select Target Branch --</option>
                @foreach(\App\Models\Branch::where('tenant_id', session('hrms_tenant_id'))->where('id','!=',$branch->id)->where('is_active',true)->get() as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 form-group d-flex align-items-end">
              <button type="submit" class="btn btn-warning btn-block">Move</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    @endif
  </div>
</div>
@endsection

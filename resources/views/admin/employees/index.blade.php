@extends('layouts.adminlte')
@section('title','Employees')
@section('page-title','Employee Directory')
@section('breadcrumb','Employees')
@section('content')
<!-- Filter Bar -->
<div class="card mb-3">
  <div class="card-body p-3">
    <form method="GET" class="form-inline flex-wrap">
      <div class="form-group mr-2 mb-2">
        <label class="mr-1"><i class="fas fa-code-branch mr-1"></i>Branch:</label>
        <select name="branch_id" class="form-control form-control-sm">
          <option value="">All Branches</option>
          @foreach($branches as $b)
          <option value="{{ $b->id }}" {{ $branchFilter == $b->id ? 'selected' : '' }}>
            {{ $b->name }} {{ $b->is_head_office ? '⭐' : '' }}
          </option>
          @endforeach
        </select>
      </div>
      <div class="form-group mr-2 mb-2">
        <label class="mr-1">Dept:</label>
        <select name="department" class="form-control form-control-sm">
          <option value="">All Departments</option>
          @foreach($departments as $d)
          <option value="{{ $d }}" {{ $deptFilter === $d ? 'selected' : '' }}>{{ $d }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group mr-2 mb-2">
        <label class="mr-1">Status:</label>
        <select name="status" class="form-control form-control-sm">
          <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>All</option>
          <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="terminated" {{ $statusFilter === 'terminated' ? 'selected' : '' }}>Terminated</option>
        </select>
      </div>
      <button type="submit" class="btn btn-sm btn-primary mb-2 mr-2"><i class="fas fa-filter mr-1"></i>Filter</button>
      <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-default mb-2 mr-2">Clear</a>
      <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success mb-2 ml-auto">
        <i class="fas fa-user-plus mr-1"></i>Add Employee
      </a>
    </form>
  </div>
</div>

<!-- Active filter pills -->
@if($branchFilter || $deptFilter)
<div class="mb-3">
  @if($branchFilter)
  @php $fb = $branches->firstWhere('id', $branchFilter); @endphp
  <span class="badge badge-info px-3 py-2 mr-2"><i class="fas fa-code-branch mr-1"></i>Branch: {{ $fb->name ?? '' }}</span>
  @endif
  @if($deptFilter)
  <span class="badge badge-secondary px-3 py-2"><i class="fas fa-sitemap mr-1"></i>Dept: {{ $deptFilter }}</span>
  @endif
</div>
@endif

<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-users mr-2"></i>Employees
      <span class="badge badge-light ml-2">{{ $employees->total() }}</span>
    </h3>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr><th>Name</th><th>Branch</th><th>Department</th><th>Position</th><th>Type</th><th>Annual CTC</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($employees as $emp)
        <tr>
          <td>
            <strong>{{ $emp->full_name }}</strong><br>
            <small class="text-muted">{{ $emp->email }}</small>
          </td>
          <td>
            @if($emp->branch)
            <span class="badge badge-{{ $emp->branch->is_head_office ? 'warning' : 'secondary' }}">
              {{ $emp->branch->is_head_office ? '⭐ ' : '' }}{{ $emp->branch->city }}
            </span>
            @else
            <span class="text-muted small">—</span>
            @endif
          </td>
          <td>{{ $emp->department }}</td>
          <td>{{ $emp->position }}</td>
          <td><span class="badge badge-light">{{ ucfirst(str_replace('_',' ',$emp->employment_type)) }}</span></td>
          <td>₹{{ number_format($emp->salary, 0) }}</td>
          <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : ($emp->status === 'terminated' ? 'danger' : 'secondary') }}">{{ ucfirst($emp->status) }}</span></td>
          <td>
            <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
            <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-xs btn-danger" onclick="return confirm('Delete employee?')"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-5 text-muted">
          <i class="fas fa-users fa-2x d-block mb-2"></i>No employees found.
          <a href="{{ route('admin.employees.create') }}">Add the first employee</a>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $employees->links() }}</div>
</div>
@endsection

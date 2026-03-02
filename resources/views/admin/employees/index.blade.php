@extends('layouts.adminlte')
@section('title', 'Employees')
@section('page-title', 'Employee Management')
@section('breadcrumb', 'Employees')
@section('content')
<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-users mr-2"></i>All Employees</h3>
    <div class="card-tools">
      <a href="{{ route('admin.employees.create') }}" class="btn btn-sm btn-success">
        <i class="fas fa-user-plus mr-1"></i>Add Employee
      </a>
    </div>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-striped mb-0">
      <thead class="thead-dark">
        <tr><th>Employee</th><th>Department</th><th>Position</th><th>Type</th><th>Hire Date</th><th>Salary</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($employees as $emp)
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#2d6a9f,#4db8ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:14px;flex-shrink:0;margin-right:10px">
                {{ strtoupper(substr($emp->first_name, 0, 1)) }}
              </div>
              <div><strong>{{ $emp->full_name }}</strong><br><small class="text-muted">{{ $emp->email }}</small></div>
            </div>
          </td>
          <td>{{ $emp->department }}</td>
          <td>{{ $emp->position }}</td>
          <td><span class="badge badge-secondary">{{ str_replace('_', ' ', ucfirst($emp->employment_type)) }}</span></td>
          <td>{{ $emp->hire_date->format('M d, Y') }}</td>
          <td>${{ number_format($emp->salary, 0) }}/yr</td>
          <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : ($emp->status === 'inactive' ? 'warning' : 'danger') }}">{{ ucfirst($emp->status) }}</span></td>
          <td>
            <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>
            <a href="{{ route('admin.employees.edit', $emp->id) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete {{ $emp->full_name }}?')"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-users fa-2x d-block mb-2"></i>No employees found. Add your first employee.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $employees->links() }}</div>
</div>
@endsection

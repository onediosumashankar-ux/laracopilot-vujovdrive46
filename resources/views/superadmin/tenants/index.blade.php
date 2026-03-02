@extends('layouts.adminlte')
@section('title', 'Tenants')
@section('page-title', 'Tenant Management')
@section('breadcrumb', 'Tenants')
@section('content')
<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-building mr-2"></i>All Tenants</h3>
    <div class="card-tools">
      <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-sm btn-light">
        <i class="fas fa-plus mr-1"></i>Add Tenant
      </a>
    </div>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-striped mb-0">
      <thead class="thead-dark">
        <tr><th>Name</th><th>Domain</th><th>Email</th><th>Plan</th><th>Employees</th><th>Status</th><th>Created</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($tenants as $tenant)
        <tr>
          <td><strong>{{ $tenant->name }}</strong></td>
          <td><code>{{ $tenant->domain }}</code></td>
          <td>{{ $tenant->email }}</td>
          <td><span class="badge badge-{{ $tenant->plan === 'enterprise' ? 'primary' : ($tenant->plan === 'professional' ? 'info' : 'secondary') }}">{{ ucfirst($tenant->plan) }}</span></td>
          <td>{{ $tenant->employees_count }}</td>
          <td><span class="badge badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'danger' : 'warning') }}">{{ ucfirst($tenant->status) }}</span></td>
          <td>{{ $tenant->created_at->format('M d, Y') }}</td>
          <td>
            <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}" class="btn btn-xs btn-warning mr-1"><i class="fas fa-edit"></i></a>
            <form action="{{ route('superadmin.tenants.destroy', $tenant->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete tenant {{ $tenant->name }}? This will remove all associated data.')"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-building fa-2x mb-2 d-block"></i>No tenants found. Create your first tenant.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $tenants->links() }}</div>
</div>
@endsection

@extends('layouts.adminlte')
@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')
@section('breadcrumb', 'Dashboard')
@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-primary">
      <div class="inner"><h3>{{ $totalTenants }}</h3><p>Total Tenants</p></div>
      <div class="icon"><i class="fas fa-building"></i></div>
      <a href="{{ route('superadmin.tenants.index') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner"><h3>{{ $activeTenants }}</h3><p>Active Tenants</p></div>
      <div class="icon"><i class="fas fa-check-circle"></i></div>
      <a href="{{ route('superadmin.tenants.index') }}" class="small-box-footer">View Active <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner"><h3>{{ $totalEmployees }}</h3><p>Total Employees</p></div>
      <div class="icon"><i class="fas fa-users"></i></div>
      <a href="#" class="small-box-footer">Across All Tenants <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner"><h3>{{ $totalUsers }}</h3><p>Total Users</p></div>
      <div class="icon"><i class="fas fa-user-shield"></i></div>
      <a href="#" class="small-box-footer">All Roles <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-building mr-2"></i>Tenant Overview</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
          <thead class="thead-light">
            <tr><th>Tenant</th><th>Plan</th><th>Employees</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($tenantStats as $tenant)
            <tr>
              <td><strong>{{ $tenant->name }}</strong><br><small class="text-muted">{{ $tenant->domain }}</small></td>
              <td><span class="badge badge-{{ $tenant->plan === 'enterprise' ? 'primary' : ($tenant->plan === 'professional' ? 'info' : 'secondary') }}">{{ ucfirst($tenant->plan) }}</span></td>
              <td><span class="badge badge-light">{{ $tenant->employees_count }}</span></td>
              <td><span class="badge badge-{{ $tenant->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($tenant->status) }}</span></td>
              <td>
                <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                <form action="{{ route('superadmin.tenants.destroy', $tenant->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete this tenant?')"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No tenants found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>Quick Actions</h3>
      </div>
      <div class="card-body">
        <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary btn-block mb-2">
          <i class="fas fa-building mr-2"></i>Add New Tenant
        </a>
        <div class="info-box bg-light mt-3">
          <span class="info-box-icon"><i class="fas fa-chart-pie text-primary"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Active Rate</span>
            <span class="info-box-number">{{ $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100) : 0 }}%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

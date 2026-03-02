@extends('layouts.adminlte')
@section('title','Branch Management')
@section('page-title','Branch Management')
@section('breadcrumb','Branches')
@section('content')
<div class="row">
  <div class="col-md-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3>{{ $totalBranches }}</h3><p>Total Branches</p></div><div class="icon"><i class="fas fa-code-branch"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $activeBranches }}</h3><p>Active Branches</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>{{ $totalEmployees }}</h3><p>Total Employees</p></div><div class="icon"><i class="fas fa-users"></i></div></div></div>
  <div class="col-md-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>{{ $headOffice ? $headOffice->city : 'N/A' }}</h3><p>Head Office City</p></div><div class="icon"><i class="fas fa-building"></i></div></div></div>
</div>

<div class="mb-3">
  <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
    <i class="fas fa-plus mr-1"></i>Add Branch
  </a>
</div>

<div class="row">
  @forelse($branches as $branch)
  <div class="col-md-4 mb-4">
    <div class="card h-100 {{ $branch->is_head_office ? 'border-warning' : '' }}" style="{{ $branch->is_head_office ? 'border-width:2px' : '' }}">
      <div class="card-header" style="background:{{ $branch->is_head_office ? 'linear-gradient(135deg,#b8860b,#daa520)' : 'linear-gradient(135deg,#1a2942,#2d6a9f)' }};color:#fff">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="mb-0">{{ $branch->name }}</h5>
            @if($branch->code)<code style="color:rgba(255,255,255,.8);font-size:.75rem">{{ $branch->code }}</code>@endif
          </div>
          <div class="text-right">
            @if($branch->is_head_office)<span class="badge badge-warning"><i class="fas fa-star mr-1"></i>HQ</span>@endif
            <span class="badge badge-{{ $branch->is_active ? 'success' : 'secondary' }} ml-1">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row text-center mb-3">
          <div class="col-6">
            <div class="text-muted small">Employees</div>
            <strong class="h4">{{ $branch->employees_count }}</strong>
          </div>
          <div class="col-6">
            <div class="text-muted small">City</div>
            <strong>{{ $branch->city }}</strong>
          </div>
        </div>
        @if($branch->address)
        <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $branch->address }}, {{ $branch->city }} – {{ $branch->pincode }}</p>
        @endif
        @if($branch->phone)
        <p class="small text-muted mb-1"><i class="fas fa-phone mr-1"></i>{{ $branch->phone }}</p>
        @endif
        @if($branch->manager_name)
        <p class="small text-muted mb-1"><i class="fas fa-user-tie mr-1"></i>{{ $branch->manager_name }}</p>
        @endif
      </div>
      <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye mr-1"></i>View</a>
        <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
        <a href="{{ route('admin.employees.index') }}?branch_id={{ $branch->id }}" class="btn btn-sm btn-info"><i class="fas fa-users mr-1"></i>Employees</a>
        <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this branch?')"><i class="fas fa-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="callout callout-info">
      No branches yet. <a href="{{ route('admin.branches.create') }}">Create your first branch</a>.
    </div>
  </div>
  @endforelse
</div>
@endsection

@extends('layouts.adminlte')
@section('title','Salary Structures')
@section('page-title','Salary Structure Templates')
@section('breadcrumb','Salary Structures')
@section('content')
<div class="mb-3 d-flex justify-content-between">
  <div>
    <a href="{{ route('admin.salary.create') }}" class="btn btn-primary mr-2"><i class="fas fa-plus mr-1"></i>New Structure</a>
    <a href="{{ route('admin.salary.assign') }}" class="btn btn-info mr-2"><i class="fas fa-user-tag mr-1"></i>Assign to Employee</a>
    <a href="{{ route('admin.salary.offers') }}" class="btn btn-success"><i class="fas fa-file-contract mr-1"></i>Offer Letters</a>
  </div>
</div>
<div class="row">
  @forelse($structures as $s)
  <div class="col-md-4 mb-4">
    <div class="card h-100 {{ !$s->is_active ? 'border-secondary' : '' }}">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $s->name }}</h5>
          <span class="badge badge-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        @if($s->code)<small style="opacity:.7">{{ $s->code }}</small>@endif
      </div>
      <div class="card-body">
        <div class="row text-center mb-3">
          <div class="col-4"><div class="text-muted small">CTC</div><strong>₹{{ number_format($s->ctc_amount, 0) }}</strong></div>
          <div class="col-4"><div class="text-muted small">Components</div><strong>{{ $s->components_count }}</strong></div>
          <div class="col-4"><div class="text-muted small">Assigned</div><strong>{{ $s->employee_assignments_count }}</strong></div>
        </div>
        @if($s->description)<p class="text-muted small">{{ Str::limit($s->description, 80) }}</p>@endif
        <span class="badge badge-secondary">{{ ucfirst($s->type) }}</span>
      </div>
      <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('admin.salary.show', $s->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye mr-1"></i>View</a>
        <a href="{{ route('admin.salary.edit', $s->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
        <form action="{{ route('admin.salary.destroy', $s->id) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this structure?')"><i class="fas fa-trash"></i></button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12"><div class="callout callout-info">No salary structures yet. <a href="{{ route('admin.salary.create') }}">Create your first structure</a>.</div></div>
  @endforelse
</div>
<div class="mt-2">{{ $structures->links() }}</div>
@endsection

@extends('layouts.adminlte')
@section('title', 'Holiday Calendar')
@section('page-title', 'Holiday Calendar')
@section('breadcrumb', 'Holidays')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>Add Holiday</h3>
      </div>
      <form action="{{ route('admin.payroll.holidays.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label>Holiday Name *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Christmas Day" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Type *</label>
            <select name="type" class="form-control" required>
              <option value="public" {{ old('type') === 'public' ? 'selected' : '' }}>Public Holiday (National)</option>
              <option value="company" {{ old('type') === 'company' ? 'selected' : '' }}>Company Holiday</option>
              <option value="optional" {{ old('type') === 'optional' ? 'selected' : '' }}>Optional Holiday</option>
            </select>
          </div>
          <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Optional description">
          </div>
          <div class="form-group">
            <div class="icheck-primary">
              <input type="checkbox" name="recurring" id="recurring" value="1" {{ old('recurring') ? 'checked' : '' }}>
              <label for="recurring">Recurring (repeat every year)</label>
            </div>
          </div>
          <div class="alert alert-info small p-2">
            <i class="fas fa-info-circle mr-1"></i>
            Holidays falling on weekends are automatically excluded from payroll calculations.
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i>Add Holiday</button>
        </div>
      </form>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Holiday List</h3>
        <div class="card-tools">
          <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left mr-1"></i>Back to Payroll</a>
        </div>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
          <thead class="thead-dark">
            <tr><th>Date</th><th>Day</th><th>Holiday Name</th><th>Type</th><th>Recurring</th><th>Action</th></tr>
          </thead>
          <tbody>
            @forelse($holidays as $h)
            <tr class="{{ $h->date->isPast() ? 'text-muted' : '' }}">
              <td><strong>{{ $h->date->format('d M Y') }}</strong></td>
              <td><span class="badge badge-{{ $h->date->isWeekend() ? 'secondary' : 'light text-dark' }}">{{ $h->date->format('l') }}</span></td>
              <td>
                {{ $h->name }}
                @if($h->description)<br><small class="text-muted">{{ $h->description }}</small>@endif
                @if($h->date->isWeekend())<br><small class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Falls on weekend – no payroll impact</small>@endif
              </td>
              <td>
                <span class="badge badge-{{ $h->type === 'public' ? 'info' : ($h->type === 'company' ? 'primary' : 'secondary') }}">
                  {{ ucfirst($h->type) }}
                </span>
              </td>
              <td>{{ $h->recurring ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>' }}</td>
              <td>
                <form action="{{ route('admin.payroll.holidays.destroy', $h->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Remove this holiday?')"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-5">
              <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
              No holidays added yet. Add holidays to ensure accurate payroll calculations.
            </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">{{ $holidays->links() }}</div>
    </div>
  </div>
</div>
@endsection

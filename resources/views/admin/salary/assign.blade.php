@extends('layouts.adminlte')
@section('title','Assign Salary Structure')
@section('page-title','Assign Salary Structure to Employee')
@section('breadcrumb','Assignment')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Assign Structure</h3>
      </div>
      <form action="{{ route('admin.salary.assign.preview') }}" method="POST" id="previewForm">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label class="font-weight-bold">Employee *</label>
            <select name="employee_id" class="form-control" required>
              <option value="">-- Select Employee --</option>
              @foreach($employees as $emp)
              <option value="{{ $emp->id }}">{{ $emp->full_name }} – {{ $emp->department }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="font-weight-bold">Salary Structure *</label>
            <select name="salary_structure_id" class="form-control" required>
              <option value="">-- Select Structure --</option>
              @foreach($structures as $s)
              <option value="{{ $s->id }}">{{ $s->name }} (₹{{ number_format($s->ctc_amount,0) }}/{{ $s->type }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="font-weight-bold">CTC Override ₹ <small class="text-muted">(leave blank to use structure default)</small></label>
            <input type="number" name="ctc_override" class="form-control" placeholder="e.g. 720000">
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-info btn-block">
            <i class="fas fa-calculator mr-1"></i>Preview Breakdown
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Current Assignments</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-dark">
            <tr><th>Employee</th><th>Structure</th><th>CTC</th><th>Effective</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($assignments as $a)
            <tr>
              <td><strong>{{ $a->employee->full_name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $a->employee->department ?? '' }}</small></td>
              <td>{{ $a->salaryStructure->name ?? 'N/A' }}</td>
              <td>₹{{ number_format($a->effective_ctc, 0) }}<br><small class="text-muted">{{ $a->salaryStructure->type ?? '' }}</small></td>
              <td>{{ $a->effective_from->format('d M Y') }}</td>
              <td>
                <a href="{{ route('admin.salary.breakdown', $a->id) }}" class="btn btn-xs btn-info"><i class="fas fa-table"></i></a>
                <a href="{{ route('admin.salary.offer.create') }}?employee_id={{ $a->employee_id }}" class="btn btn-xs btn-success"><i class="fas fa-file-contract"></i></a>
                <a href="{{ route('admin.tds.declare', $a->employee_id) }}" class="btn btn-xs btn-warning"><i class="fas fa-file-invoice"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No assignments yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="card-footer">{{ $assignments->links() }}</div>
    </div>
  </div>
</div>
@endsection

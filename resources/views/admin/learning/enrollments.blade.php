@extends('layouts.adminlte')
@section('title', 'Enrollments')
@section('page-title', 'Enrollments – ' . $program->title)
@section('breadcrumb', 'Enrollments')
@section('content')
<div class="mb-3">
  <a href="{{ route('admin.learning.show', $program->id) }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Back to Program</a>
</div>
<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-users mr-2"></i>All Enrollments</h3>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr><th>Employee</th><th>Schedule Slot</th><th>Enrolled On</th><th>Reschedules</th><th>Previous Slot</th><th>Attendance</th><th>Status</th><th>Update</th></tr>
      </thead>
      <tbody>
        @forelse($enrollments as $enr)
        <tr>
          <td>
            <strong>{{ $enr->employee->full_name ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $enr->employee->department ?? '' }}</small>
          </td>
          <td>
            @if($enr->trainingSchedule)
              <span class="badge badge-info">{{ $enr->trainingSchedule->label }}</span><br>
              <small class="text-muted">
                {{ \Carbon\Carbon::parse($enr->trainingSchedule->start_time)->format('h:i A') }}
                – {{ \Carbon\Carbon::parse($enr->trainingSchedule->end_time)->format('h:i A') }}
                &bull; {{ $enr->trainingSchedule->days_label }}
              </small>
            @else
              <span class="text-muted">Not selected</span>
            @endif
          </td>
          <td>{{ $enr->created_at->format('d M Y') }}</td>
          <td>
            @if($enr->reschedule_count > 0)
              <span class="badge badge-warning">{{ $enr->reschedule_count }}×</span><br>
              <small class="text-muted">{{ $enr->rescheduled_at ? $enr->rescheduled_at->format('d M') : '' }}</small>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            {{ $enr->previousSchedule->label ?? '—' }}
            @if($enr->reschedule_reason)
            <br><small class="text-muted">{{ Str::limit($enr->reschedule_reason, 40) }}</small>
            @endif
          </td>
          <td>
            <span class="badge badge-{{ $enr->attendance_status === 'completed' ? 'success' : ($enr->attendance_status === 'absent' ? 'danger' : ($enr->attendance_status === 'attending' ? 'primary' : 'secondary')) }}">
              {{ ucfirst(str_replace('_',' ',$enr->attendance_status)) }}
            </span>
          </td>
          <td><span class="badge badge-{{ $enr->status === 'completed' ? 'success' : ($enr->status === 'dropped' ? 'danger' : 'info') }}">{{ ucfirst($enr->status) }}</span></td>
          <td>
            <form action="{{ route('admin.learning.enrollments.update', $enr->id) }}" method="POST" class="form-inline">
              @csrf @method('PUT')
              <select name="status" class="form-control form-control-sm mr-1" style="width:90px">
                <option value="enrolled" {{ $enr->status === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                <option value="in_progress" {{ $enr->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $enr->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="dropped" {{ $enr->status === 'dropped' ? 'selected' : '' }}>Dropped</option>
              </select>
              <select name="attendance_status" class="form-control form-control-sm mr-1" style="width:100px">
                <option value="not_started" {{ $enr->attendance_status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                <option value="attending" {{ $enr->attendance_status === 'attending' ? 'selected' : '' }}>Attending</option>
                <option value="completed" {{ $enr->attendance_status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="absent" {{ $enr->attendance_status === 'absent' ? 'selected' : '' }}>Absent</option>
              </select>
              <input type="number" name="score" class="form-control form-control-sm mr-1" style="width:60px" placeholder="Score" value="{{ $enr->score }}" min="0" max="100">
              <button class="btn btn-xs btn-success"><i class="fas fa-check"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-users fa-2x d-block mb-2"></i>No enrollments yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $enrollments->links() }}</div>
</div>
@endsection

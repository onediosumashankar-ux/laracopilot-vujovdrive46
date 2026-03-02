@extends('layouts.adminlte')
@section('title', 'My Enrollments')
@section('page-title', 'My Training Enrollments')
@section('breadcrumb', 'My Enrollments')
@section('content')
<div class="mb-3">
  <a href="{{ route('employee.learning') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Browse Programs</a>
</div>

@forelse($enrollments as $enr)
<div class="card mb-3">
  <div class="card-header"
       style="background:{{ $enr->status === 'completed' ? 'linear-gradient(135deg,#28a745,#20c997)' : ($enr->status === 'dropped' ? '#6c757d' : 'linear-gradient(135deg,#1a2942,#2d6a9f)') }};color:#fff">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ $enr->trainingProgram->title ?? 'N/A' }}</h5>
      <div>
        <span class="badge badge-light mr-2">{{ ucfirst(str_replace('_',' ',$enr->attendance_status)) }}</span>
        <span class="badge badge-{{ $enr->status === 'completed' ? 'success' : ($enr->status === 'dropped' ? 'secondary' : 'warning') }}">
          {{ ucfirst($enr->status) }}
        </span>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-5">
        <h6 class="font-weight-bold"><i class="fas fa-calendar-check mr-1 text-primary"></i>Current Schedule</h6>
        @if($enr->trainingSchedule)
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted pl-0" style="width:35%">Slot</td><td><strong>{{ $enr->trainingSchedule->label }}</strong></td></tr>
          <tr><td class="text-muted pl-0">Dates</td><td>{{ $enr->trainingSchedule->start_date->format('d M') }} – {{ $enr->trainingSchedule->end_date->format('d M Y') }}</td></tr>
          <tr><td class="text-muted pl-0">Time</td><td>{{ \Carbon\Carbon::parse($enr->trainingSchedule->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($enr->trainingSchedule->end_time)->format('h:i A') }}</td></tr>
          <tr><td class="text-muted pl-0">Days</td><td>{{ $enr->trainingSchedule->days_label }}</td></tr>
          @if($enr->trainingSchedule->venue)<tr><td class="text-muted pl-0">Venue</td><td>{{ $enr->trainingSchedule->venue }}</td></tr>@endif
          @if($enr->trainingSchedule->instructor)<tr><td class="text-muted pl-0">Instructor</td><td>{{ $enr->trainingSchedule->instructor }}</td></tr>@endif
        </table>
        @else
        <div class="text-muted">No schedule selected.</div>
        @endif
      </div>
      <div class="col-md-4">
        @if($enr->reschedule_count > 0)
        <h6 class="font-weight-bold"><i class="fas fa-history mr-1 text-warning"></i>Reschedule History</h6>
        <div class="small">
          <div><strong>Times rescheduled:</strong> {{ $enr->reschedule_count }}</div>
          @if($enr->rescheduled_at)<div><strong>Last change:</strong> {{ $enr->rescheduled_at->format('d M Y h:i A') }}</div>@endif
          @if($enr->previousSchedule)<div><strong>Previous slot:</strong> {{ $enr->previousSchedule->label }}</div>@endif
          @if($enr->reschedule_reason)<div class="text-muted mt-1"><em>"{{ $enr->reschedule_reason }}"</em></div>@endif
        </div>
        @endif
        @if($enr->score)
        <div class="mt-3">
          <h6 class="font-weight-bold"><i class="fas fa-star mr-1 text-warning"></i>Score</h6>
          <div class="display-4 font-weight-bold text-{{ $enr->score >= 80 ? 'success' : ($enr->score >= 60 ? 'warning' : 'danger') }}">{{ $enr->score }}<small class="h6">/100</small></div>
        </div>
        @endif
      </div>
      <div class="col-md-3 text-right">
        @if(!in_array($enr->status, ['completed', 'dropped']))
        <a href="{{ route('employee.learning.reschedule', $enr->id) }}" class="btn btn-warning btn-block mb-2">
          <i class="fas fa-exchange-alt mr-1"></i>Change Schedule
        </a>
        <form action="{{ route('employee.learning.cancel', $enr->id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-block btn-sm"
            onclick="return confirm('Cancel this enrollment?')">
            <i class="fas fa-times mr-1"></i>Cancel Enrollment
          </button>
        </form>
        @elseif($enr->status === 'dropped')
        <a href="{{ route('employee.learning.schedules', $enr->training_program_id) }}" class="btn btn-primary btn-block">
          <i class="fas fa-redo mr-1"></i>Re-Enroll
        </a>
        @endif
      </div>
    </div>
  </div>
</div>
@empty
<div class="callout callout-info">
  <h5>No enrollments yet!</h5>
  <p>Browse available training programs and pick a schedule that works for you.</p>
  <a href="{{ route('employee.learning') }}" class="btn btn-primary"><i class="fas fa-graduation-cap mr-1"></i>Browse Programs</a>
</div>
@endforelse
<div class="mt-2">{{ $enrollments->links() }}</div>
@endsection

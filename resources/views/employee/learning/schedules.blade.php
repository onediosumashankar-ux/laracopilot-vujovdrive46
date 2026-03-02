@extends('layouts.adminlte')
@section('title', 'Choose Schedule')
@section('page-title', 'Choose Your Schedule Slot')
@section('breadcrumb', 'Enroll')
@section('content')
<div class="mb-3">
  <a href="{{ route('employee.learning') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>Back to Programs</a>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-header"><h3 class="card-title">{{ $program->title }}</h3></div>
      <div class="card-body">
        <p class="text-muted">{{ $program->description }}</p>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Duration</td><td><strong>{{ $program->duration_hours }} hours</strong></td></tr>
          <tr><td class="text-muted">Mode</td><td>{{ ucfirst($program->delivery_mode) }}</td></tr>
          <tr><td class="text-muted">Instructor</td><td>{{ $program->instructor ?? 'TBD' }}</td></tr>
          <tr><td class="text-muted">Category</td><td>{{ ucfirst(str_replace('_',' ',$program->category)) }}</td></tr>
        </table>
        @if($myEnrollment)
        <div class="alert alert-success">
          <i class="fas fa-check-circle mr-1"></i>
          You are enrolled in <strong>"{{ $myEnrollment->trainingSchedule->label ?? 'a slot' }}"</strong>.
          <a href="{{ route('employee.learning.reschedule', $myEnrollment->id) }}" class="btn btn-sm btn-warning mt-2 btn-block">
            <i class="fas fa-exchange-alt mr-1"></i>Change My Schedule
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Available Schedule Slots</h3>
      </div>
      <div class="card-body">
        @if($program->schedules->isEmpty())
        <div class="text-center text-muted py-4">
          <i class="fas fa-calendar-times fa-2x d-block mb-2"></i>No schedule slots available yet.
        </div>
        @else
        <div class="row">
          @foreach($program->schedules as $sch)
          <div class="col-md-6 mb-3">
            <div class="card {{ $sch->status === 'open' ? 'border-success' : 'border-secondary' }} h-100"
                 style="border-width:2px;border-radius:10px">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="font-weight-bold mb-0">{{ $sch->label }}</h6>
                  <span class="badge badge-{{ $sch->status === 'open' ? 'success' : ($sch->status === 'full' ? 'warning' : 'danger') }}">
                    {{ ucfirst($sch->status) }}
                  </span>
                </div>
                <div class="small mb-1">
                  <i class="fas fa-calendar mr-1 text-muted"></i>
                  {{ $sch->start_date->format('d M') }} – {{ $sch->end_date->format('d M Y') }}
                </div>
                <div class="small mb-1">
                  <i class="fas fa-clock mr-1 text-muted"></i>
                  {{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }}
                  – {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}
                </div>
                <div class="small mb-1">
                  <i class="fas fa-calendar-week mr-1 text-muted"></i>{{ $sch->days_label }}
                </div>
                @if($sch->venue)
                <div class="small mb-1">
                  <i class="fas fa-map-marker-alt mr-1 text-muted"></i>{{ $sch->venue }}
                </div>
                @endif
                @if($sch->instructor)
                <div class="small mb-2">
                  <i class="fas fa-chalkboard-teacher mr-1 text-muted"></i>{{ $sch->instructor }}
                </div>
                @endif
                <div class="progress mb-2" style="height:6px">
                  <div class="progress-bar bg-{{ $sch->available_seats == 0 ? 'danger' : 'success' }}"
                       style="width:{{ $sch->max_seats > 0 ? ($sch->booked_seats/$sch->max_seats*100) : 0 }}%"></div>
                </div>
                <div class="small text-muted mb-3">
                  <i class="fas fa-users mr-1"></i>{{ $sch->available_seats }} seats available
                </div>

                @if(!$myEnrollment && $sch->status === 'open')
                <form action="{{ route('employee.learning.enroll', $program->id) }}" method="POST">
                  @csrf
                  <input type="hidden" name="training_schedule_id" value="{{ $sch->id }}">
                  <button type="submit" class="btn btn-success btn-sm btn-block">
                    <i class="fas fa-check mr-1"></i>Enroll in This Slot
                  </button>
                </form>
                @elseif($myEnrollment && $myEnrollment->training_schedule_id == $sch->id)
                <div class="btn btn-success btn-sm btn-block disabled">
                  <i class="fas fa-check-circle mr-1"></i>Your Current Slot
                </div>
                @elseif($sch->status !== 'open')
                <button class="btn btn-secondary btn-sm btn-block" disabled>
                  <i class="fas fa-ban mr-1"></i>{{ ucfirst($sch->status) }}
                </button>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

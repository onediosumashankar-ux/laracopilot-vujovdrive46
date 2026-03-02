@extends('layouts.adminlte')
@section('title', 'Change Schedule')
@section('page-title', 'Change Training Schedule')
@section('breadcrumb', 'Reschedule')
@section('content')
<div class="mb-3">
  <a href="{{ route('employee.learning.my') }}" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i>My Enrollments</a>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="card card-warning card-outline">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle mr-1"></i>Current Schedule</h3></div>
      <div class="card-body">
        <h5 class="font-weight-bold">{{ $enrollment->trainingProgram->title }}</h5>
        @if($enrollment->trainingSchedule)
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted pl-0">Slot</td><td><strong>{{ $enrollment->trainingSchedule->label }}</strong></td></tr>
          <tr><td class="text-muted pl-0">Dates</td><td>{{ $enrollment->trainingSchedule->start_date->format('d M') }} – {{ $enrollment->trainingSchedule->end_date->format('d M Y') }}</td></tr>
          <tr><td class="text-muted pl-0">Time</td><td>{{ \Carbon\Carbon::parse($enrollment->trainingSchedule->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($enrollment->trainingSchedule->end_time)->format('h:i A') }}</td></tr>
          <tr><td class="text-muted pl-0">Days</td><td>{{ $enrollment->trainingSchedule->days_label }}</td></tr>
        </table>
        @endif
        <div class="alert alert-info p-2 small mt-2">
          <i class="fas fa-exchange-alt mr-1"></i>
          You have rescheduled <strong>{{ $enrollment->reschedule_count }} time(s)</strong> so far.
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Pick a New Schedule Slot</h3>
      </div>
      <form action="{{ route('employee.learning.reschedule.save', $enrollment->id) }}" method="POST">
        @csrf
        <div class="card-body">
          @if($availableSchedules->isEmpty())
          <div class="text-center text-muted py-4">
            <i class="fas fa-calendar-times fa-3x d-block mb-3"></i>
            No other open slots available at this time.
          </div>
          @else
          <div class="row">
            @foreach($availableSchedules as $sch)
            <div class="col-md-6 mb-3">
              <div class="card border" style="border-radius:10px;cursor:pointer" onclick="document.getElementById('slot_{{ $sch->id }}').checked=true;document.querySelectorAll('.slot-card').forEach(c=>c.style.borderColor='#dee2e6');this.style.borderColor='#28a745';this.style.borderWidth='2px'">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center mb-2">
                    <input type="radio" name="training_schedule_id" id="slot_{{ $sch->id }}" value="{{ $sch->id }}" class="mr-2" required>
                    <label for="slot_{{ $sch->id }}" class="font-weight-bold mb-0 slot-card">{{ $sch->label }}</label>
                  </div>
                  <div class="small text-muted">
                    <div><i class="fas fa-calendar mr-1"></i>{{ $sch->start_date->format('d M') }} – {{ $sch->end_date->format('d M Y') }}</div>
                    <div><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}</div>
                    <div><i class="fas fa-calendar-week mr-1"></i>{{ $sch->days_label }}</div>
                    @if($sch->venue)<div><i class="fas fa-map-marker-alt mr-1"></i>{{ $sch->venue }}</div>@endif
                    @if($sch->instructor)<div><i class="fas fa-chalkboard-teacher mr-1"></i>{{ $sch->instructor }}</div>@endif
                  </div>
                  <div class="progress mt-2" style="height:5px">
                    <div class="progress-bar bg-{{ $sch->available_seats > 5 ? 'success' : 'warning' }}"
                         style="width:{{ $sch->max_seats > 0 ? (($sch->max_seats-$sch->available_seats)/$sch->max_seats*100) : 0 }}%"></div>
                  </div>
                  <small class="text-muted">{{ $sch->available_seats }} seats left</small>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          <div class="form-group mt-3">
            <label class="font-weight-bold">Reason for Rescheduling *</label>
            <textarea name="reschedule_reason" class="form-control @error('reschedule_reason') is-invalid @enderror"
              rows="3" placeholder="Please briefly explain why you need to change your schedule..." required>{{ old('reschedule_reason') }}</textarea>
            @error('reschedule_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          @endif
        </div>
        @if($availableSchedules->isNotEmpty())
        <div class="card-footer">
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-exchange-alt mr-1"></i>Confirm Schedule Change
          </button>
          <a href="{{ route('employee.learning.my') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection

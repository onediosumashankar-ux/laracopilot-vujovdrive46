@extends('layouts.adminlte')
@section('title', 'Learning & Development')
@section('page-title', 'Learning & Development')
@section('breadcrumb', 'Learning')
@section('content')
<div class="mb-3">
  <a href="{{ route('employee.learning.my') }}" class="btn btn-info">
    <i class="fas fa-bookmark mr-1"></i>My Enrollments
    @php $activeCount = collect($myEnrollments)->whereIn('status',['enrolled','in_progress'])->count(); @endphp
    @if($activeCount > 0)<span class="badge badge-light ml-1">{{ $activeCount }}</span>@endif
  </a>
</div>

<div class="row">
  @forelse($programs as $prog)
  @php $isEnrolled = in_array($prog->id, $enrolledProgramIds); @endphp
  <div class="col-md-4 mb-4">
    <div class="card h-100 {{ $isEnrolled ? 'border-success' : '' }}">
      <div class="card-header {{ $isEnrolled ? 'bg-success text-white' : '' }}" style="{{ !$isEnrolled ? 'background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff' : '' }}">
        <h5 class="card-title mb-0">{{ $prog->title }}</h5>
        @if($isEnrolled)<span class="badge badge-light"><i class="fas fa-check mr-1"></i>Enrolled</span>@endif
      </div>
      <div class="card-body">
        <p class="text-muted small">{{ Str::limit($prog->description, 100) }}</p>
        <div class="row text-center mb-3">
          <div class="col-4"><div class="text-muted small">Duration</div><strong>{{ $prog->duration_hours }}h</strong></div>
          <div class="col-4"><div class="text-muted small">Slots</div><strong class="text-{{ $prog->openSchedules->count() > 0 ? 'success' : 'danger' }}">{{ $prog->openSchedules->count() }} open</strong></div>
          <div class="col-4"><div class="text-muted small">Mode</div><strong>{{ ucfirst($prog->delivery_mode) }}</strong></div>
        </div>
        <div class="mb-2">
          <span class="badge badge-secondary mr-1">{{ ucfirst(str_replace('_',' ',$prog->category)) }}</span>
          @if($prog->instructor)<span class="badge badge-light"><i class="fas fa-chalkboard-teacher mr-1"></i>{{ $prog->instructor }}</span>@endif
        </div>
      </div>
      <div class="card-footer">
        @if($isEnrolled)
          <a href="{{ route('employee.learning.my') }}" class="btn btn-success btn-block">
            <i class="fas fa-eye mr-1"></i>View My Enrollment
          </a>
        @elseif($prog->openSchedules->count() > 0)
          <a href="{{ route('employee.learning.schedules', $prog->id) }}" class="btn btn-primary btn-block">
            <i class="fas fa-calendar-alt mr-1"></i>View Schedule Slots & Enroll
          </a>
        @else
          <button class="btn btn-secondary btn-block" disabled>
            <i class="fas fa-ban mr-1"></i>No Open Slots
          </button>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="col-12"><div class="callout callout-info">No active training programs available right now.</div></div>
  @endforelse
</div>
@endsection

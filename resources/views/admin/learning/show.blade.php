@extends('layouts.adminlte')
@section('title', $program->title)
@section('page-title', $program->title)
@section('breadcrumb', 'Program Detail')
@section('content')
<div class="row">
  <!-- Program Info -->
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-body">
        <h5 class="font-weight-bold">{{ $program->title }}</h5>
        <p class="text-muted">{{ $program->description }}</p>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Category</td><td>{{ ucfirst(str_replace('_',' ',$program->category)) }}</td></tr>
          <tr><td class="text-muted">Mode</td><td>{{ ucfirst($program->delivery_mode) }}</td></tr>
          <tr><td class="text-muted">Duration</td><td>{{ $program->duration_hours }}h</td></tr>
          <tr><td class="text-muted">Instructor</td><td>{{ $program->instructor ?? 'TBD' }}</td></tr>
          <tr><td class="text-muted">Schedules</td><td><span class="badge badge-info">{{ $program->schedules->count() }}</span></td></tr>
          <tr><td class="text-muted">Enrolled</td><td><span class="badge badge-success">{{ $program->enrollments_count }}</span></td></tr>
          <tr><td class="text-muted">Status</td><td><span class="badge badge-{{ $program->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($program->status) }}</span></td></tr>
        </table>
        <a href="{{ route('admin.learning.edit', $program->id) }}" class="btn btn-warning btn-block"><i class="fas fa-edit mr-1"></i>Edit Program</a>
        <a href="{{ route('admin.learning.enrollments', $program->id) }}" class="btn btn-info btn-block mt-1"><i class="fas fa-users mr-1"></i>View All Enrollments</a>
      </div>
    </div>
  </div>

  <!-- Schedule Slots -->
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Schedule Slots</h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addScheduleModal">
            <i class="fas fa-plus mr-1"></i>Add Slot
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        @if($program->schedules->isEmpty())
        <div class="text-center text-muted py-5">
          <i class="fas fa-calendar-times fa-3x d-block mb-3"></i>
          No schedule slots yet. Add slots so employees can enroll.
        </div>
        @else
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-dark">
            <tr><th>Slot Label</th><th>Dates</th><th>Time</th><th>Days</th><th>Venue/Link</th><th>Seats</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @foreach($program->schedules as $sch)
            <tr>
              <td><strong>{{ $sch->label }}</strong><br><small class="text-muted">{{ ucfirst($sch->delivery_mode) }}</small></td>
              <td>{{ $sch->start_date->format('d M') }} – {{ $sch->end_date->format('d M Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($sch->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($sch->end_time)->format('h:i A') }}</td>
              <td><span class="badge badge-secondary">{{ $sch->days_label }}</span></td>
              <td>{{ $sch->venue ?? '—' }}</td>
              <td>
                <div class="progress" style="height:6px;width:80px">
                  <div class="progress-bar bg-{{ $sch->available_seats == 0 ? 'danger' : 'success' }}"
                       style="width:{{ $sch->max_seats > 0 ? ($sch->booked_seats/$sch->max_seats*100) : 0 }}%"></div>
                </div>
                <small>{{ $sch->booked_seats }}/{{ $sch->max_seats }}</small>
              </td>
              <td><span class="badge badge-{{ $sch->status === 'open' ? 'success' : ($sch->status === 'full' ? 'warning' : 'danger') }}">{{ ucfirst($sch->status) }}</span></td>
              <td>
                <button class="btn btn-xs btn-warning edit-schedule-btn"
                  data-id="{{ $sch->id }}"
                  data-label="{{ $sch->label }}"
                  data-delivery_mode="{{ $sch->delivery_mode }}"
                  data-start_date="{{ $sch->start_date->format('Y-m-d') }}"
                  data-end_date="{{ $sch->end_date->format('Y-m-d') }}"
                  data-start_time="{{ $sch->start_time }}"
                  data-end_time="{{ $sch->end_time }}"
                  data-days_of_week="{{ $sch->days_of_week }}"
                  data-venue="{{ $sch->venue }}"
                  data-instructor="{{ $sch->instructor }}"
                  data-max_seats="{{ $sch->max_seats }}"
                  data-status="{{ $sch->status }}"
                  data-notes="{{ $sch->notes }}"
                  data-toggle="modal" data-target="#editScheduleModal">
                  <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('admin.learning.schedules.destroy', [$program->id, $sch->id]) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this slot?')"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="card mt-3">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-2"></i>Recent Enrollments</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>Employee</th><th>Schedule Slot</th><th>Status</th><th>Reschedules</th></tr></thead>
          <tbody>
            @forelse($program->enrollments->take(8) as $enr)
            <tr>
              <td>{{ $enr->employee->full_name ?? 'N/A' }}</td>
              <td>{{ $enr->trainingSchedule->label ?? '<span class="text-muted">Not selected</span>' }}</td>
              <td><span class="badge badge-{{ $enr->status === 'completed' ? 'success' : ($enr->status === 'dropped' ? 'danger' : 'info') }}">{{ ucfirst($enr->status) }}</span></td>
              <td>{{ $enr->reschedule_count > 0 ? '<span class="badge badge-warning">'.$enr->reschedule_count.' time(s)</span>' : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No enrollments yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h5 class="modal-title"><i class="fas fa-calendar-plus mr-2"></i>Add Schedule Slot</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ route('admin.learning.schedules.store', $program->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          @include('admin.learning._schedule_form')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Add Slot</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#b8860b,#daa520);color:#fff">
        <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Schedule Slot</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="editScheduleForm" action="" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
          @include('admin.learning._schedule_form', ['edit' => true])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update Slot</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.edit-schedule-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const d = this.dataset;
    const form = document.getElementById('editScheduleForm');
    form.action = '/admin/learning/{{ $program->id }}/schedules/' + d.id;
    form.querySelector('[name=label]').value = d.label;
    form.querySelector('[name=delivery_mode]').value = d.delivery_mode;
    form.querySelector('[name=start_date]').value = d.start_date;
    form.querySelector('[name=end_date]').value = d.end_date;
    form.querySelector('[name=start_time]').value = d.start_time;
    form.querySelector('[name=end_time]').value = d.end_time;
    form.querySelector('[name=days_of_week]').value = d.days_of_week;
    form.querySelector('[name=venue]').value = d.venue;
    form.querySelector('[name=instructor]').value = d.instructor;
    form.querySelector('[name=max_seats]').value = d.max_seats;
    form.querySelector('[name=status]').value = d.status;
    form.querySelector('[name=notes]').value = d.notes;
  });
});
</script>
@endpush

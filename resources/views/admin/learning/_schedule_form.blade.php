<div class="row">
  <div class="col-md-8 form-group">
    <label class="font-weight-bold">Slot Label * <small class="text-muted">(e.g. "Morning Batch", "Weekend – Slot A")</small></label>
    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
      value="{{ old('label') }}" placeholder="Morning Batch" required>
    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 form-group">
    <label class="font-weight-bold">Delivery Mode *</label>
    <select name="delivery_mode" class="form-control" required>
      <option value="online">Online</option>
      <option value="classroom">Classroom</option>
      <option value="blended">Blended</option>
      <option value="self_paced">Self-Paced</option>
    </select>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Start Date *</label>
    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">End Date *</label>
    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Start Time *</label>
    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">End Time *</label>
    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '11:00') }}" required>
  </div>
  <div class="col-md-4 form-group">
    <label class="font-weight-bold">Days of Week *</label>
    <select name="days_of_week" class="form-control" required>
      <option value="weekdays">Mon – Fri (Weekdays)</option>
      <option value="mon_wed_fri">Mon / Wed / Fri</option>
      <option value="tue_thu">Tue / Thu</option>
      <option value="weekends">Sat – Sun (Weekends)</option>
      <option value="monday">Monday Only</option>
      <option value="tuesday">Tuesday Only</option>
      <option value="wednesday">Wednesday Only</option>
      <option value="thursday">Thursday Only</option>
      <option value="friday">Friday Only</option>
      <option value="saturday">Saturday Only</option>
      <option value="sunday">Sunday Only</option>
    </select>
  </div>
  <div class="col-md-4 form-group">
    <label class="font-weight-bold">Venue / Zoom Link</label>
    <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="Room 301 or https://zoom.us/...">
  </div>
  <div class="col-md-4 form-group">
    <label class="font-weight-bold">Instructor</label>
    <input type="text" name="instructor" class="form-control" value="{{ old('instructor') }}" placeholder="Instructor name">
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Max Seats *</label>
    <input type="number" name="max_seats" class="form-control" value="{{ old('max_seats', 20) }}" min="1" max="500" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Status *</label>
    <select name="status" class="form-control" required>
      <option value="open">Open for Enrollment</option>
      <option value="full">Full</option>
      <option value="cancelled">Cancelled</option>
      @isset($edit)<option value="completed">Completed</option>@endisset
    </select>
  </div>
  <div class="col-md-6 form-group">
    <label>Notes</label>
    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Any special instructions...">
  </div>
</div>

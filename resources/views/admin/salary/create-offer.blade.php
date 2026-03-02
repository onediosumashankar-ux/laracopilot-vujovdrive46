@extends('layouts.adminlte')
@section('title','Create Offer Letter')
@section('page-title','Create Offer Letter')
@section('breadcrumb','Create Offer')
@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-file-signature mr-2"></i>Generate Offer Letter</h3>
      </div>
      <form action="{{ route('admin.salary.offer.preview') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">For Employee (existing)</label>
              <select name="employee_id" class="form-control">
                <option value="">-- Select Employee (optional) --</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} – {{ $emp->department }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">For Candidate</label>
              <select name="candidate_id" class="form-control">
                <option value="">-- Select Candidate (optional) --</option>
                @foreach($candidates as $c)
                <option value="{{ $c->id }}">{{ $c->full_name }} – {{ $c->jobPosting->title ?? 'N/A' }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Salary Structure *</label>
              <select name="salary_structure_id" class="form-control" required>
                <option value="">-- Select Structure --</option>
                @foreach($structures as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Annual CTC (₹) *</label>
              <input type="number" name="ctc_annual" class="form-control" placeholder="e.g. 600000" required>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Position *</label>
              <input type="text" name="position" class="form-control" value="{{ old('position') }}" required>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Department *</label>
              <input type="text" name="department" class="form-control" value="{{ old('department') }}" required>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Employment Type *</label>
              <select name="employment_type" class="form-control" required>
                <option value="full_time">Full Time</option>
                <option value="part_time">Part Time</option>
                <option value="contract">Contract</option>
                <option value="intern">Intern</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Date of Joining *</label>
              <input type="date" name="joining_date" class="form-control" required>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Offer Valid Until *</label>
              <input type="date" name="offer_expiry" class="form-control" required>
            </div>
            <div class="col-md-12 form-group">
              <label class="font-weight-bold">Work Location</label>
              <input type="text" name="work_location" class="form-control" placeholder="Head Office / Remote / Hybrid">
            </div>
            <div class="col-md-12 form-group">
              <label class="font-weight-bold">Additional Clauses / Terms</label>
              <textarea name="custom_clauses" class="form-control" rows="4" placeholder="Probation period: 3 months&#10;Notice period: 30 days&#10;Any other special terms..."></textarea>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-info"><i class="fas fa-eye mr-1"></i>Preview Offer Letter</button>
          <a href="{{ route('admin.salary.offers') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-warning"><h3 class="card-title"><i class="fas fa-lightbulb mr-1"></i>Tips</h3></div>
      <div class="card-body small">
        <ul class="pl-3">
          <li>Select a <strong>Salary Structure</strong> to auto-populate the CTC breakdown in the offer letter.</li>
          <li>You can select either an existing employee (salary revision) or a candidate (new hire offer).</li>
          <li>The offer letter will show a complete component-wise salary breakdown.</li>
          <li><strong>Custom Clauses</strong> appear as a separate section at the bottom of the offer.</li>
          <li>After previewing, you can save as draft and mark as sent.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

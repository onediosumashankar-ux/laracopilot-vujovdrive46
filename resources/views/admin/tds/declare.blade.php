@extends('layouts.adminlte')
@section('title', 'TDS Declaration')
@section('page-title', 'TDS Investment Declaration')
@section('breadcrumb', 'Declaration')
@section('content')
<div class="row">
  <div class="col-md-9">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-file-signature mr-2"></i>{{ $employee->full_name }} – Investment Declaration FY {{ $fy }}</h3>
      </div>
      <form action="{{ route('admin.tds.declare.save', $employee->id) }}" method="POST">
        @csrf
        <input type="hidden" name="financial_year" value="{{ $fy }}">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Tax Regime *</label>
              <select name="tax_regime" class="form-control" id="regimeSelect" required>
                <option value="new" {{ ($existing && $existing->tax_regime === 'new') ? 'selected' : '' }}>New Regime</option>
                <option value="old" {{ ($existing && $existing->tax_regime === 'old') ? 'selected' : '' }}>Old Regime</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">Senior Citizen?</label>
              <select name="is_senior_citizen" class="form-control">
                <option value="0" {{ !($existing && $existing->is_senior_citizen) ? 'selected' : '' }}>No (Below 60)</option>
                <option value="1" {{ ($existing && $existing->is_senior_citizen) ? 'selected' : '' }}>Yes (60–80 years)</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="font-weight-bold">City Type</label>
              <select name="metro_city" class="form-control">
                <option value="0" {{ !($existing && $existing->metro_city) ? 'selected' : '' }}>Non-Metro</option>
                <option value="1" {{ ($existing && $existing->metro_city) ? 'selected' : '' }}>Metro City</option>
              </select>
            </div>
          </div>

          <div id="oldSection" style="display:none">
            <h6 class="font-weight-bold text-success border-bottom pb-2"><i class="fas fa-home mr-1"></i>HRA Exemption</h6>
            <div class="row">
              <div class="col-md-4 form-group"><label>HRA Received (Annual) ₹</label><input type="number" name="hra_actual" class="form-control" value="{{ old('hra_actual', $existing->hra_actual ?? 0) }}"></div>
              <div class="col-md-4 form-group"><label>Rent Paid (Annual) ₹</label><input type="number" name="rent_paid_annual" class="form-control" value="{{ old('rent_paid_annual', $existing->rent_paid_annual ?? 0) }}"></div>
              <div class="col-md-4 form-group"><label>LTA Exemption ₹</label><input type="number" name="lta_exemption" class="form-control" value="{{ old('lta_exemption', $existing->lta_exemption ?? 0) }}"></div>
            </div>
            <h6 class="font-weight-bold text-success border-bottom pb-2 mt-2"><i class="fas fa-minus-circle mr-1"></i>Chapter VI-A Deductions</h6>
            <div class="row">
              <div class="col-md-4 form-group"><label>80C – PF/LIC/ELSS ₹ <small>(max 1,50,000)</small></label><input type="number" name="section_80c" class="form-control" value="{{ old('section_80c', $existing->section_80c ?? 0) }}" max="150000"></div>
              <div class="col-md-4 form-group"><label>80CCD(1B) – NPS ₹ <small>(max 50,000)</small></label><input type="number" name="section_80ccd1b" class="form-control" value="{{ old('section_80ccd1b', $existing->section_80ccd1b ?? 0) }}" max="50000"></div>
              <div class="col-md-4 form-group"><label>80D – Health Insurance ₹</label><input type="number" name="section_80d" class="form-control" value="{{ old('section_80d', $existing->section_80d ?? 0) }}"></div>
              <div class="col-md-4 form-group"><label>80E – Education Loan ₹</label><input type="number" name="section_80e" class="form-control" value="{{ old('section_80e', $existing->section_80e ?? 0) }}"></div>
              <div class="col-md-4 form-group"><label>24B – Home Loan Interest ₹ <small>(max 2,00,000)</small></label><input type="number" name="section_24b" class="form-control" value="{{ old('section_24b', $existing->section_24b ?? 0) }}" max="200000"></div>
              <div class="col-md-4 form-group"><label>80G – Donations ₹</label><input type="number" name="section_80g" class="form-control" value="{{ old('section_80g', $existing->section_80g ?? 0) }}"></div>
              <div class="col-md-4 form-group"><label>80TTA – Savings Interest ₹ <small>(max 10,000)</small></label><input type="number" name="section_80tta" class="form-control" value="{{ old('section_80tta', $existing->section_80tta ?? 0) }}" max="10000"></div>
              <div class="col-md-4 form-group"><label>Other Deductions ₹</label><input type="number" name="other_deductions" class="form-control" value="{{ old('other_deductions', $existing->other_deductions ?? 0) }}"></div>
            </div>
          </div>

          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $existing->notes ?? '') }}</textarea>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save &amp; Calculate TDS</button>
          <a href="{{ route('admin.tds.index') }}" class="btn btn-default ml-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  function toggleRegime() {
    const v = document.getElementById('regimeSelect').value;
    document.getElementById('oldSection').style.display = v === 'old' ? 'block' : 'none';
  }
  document.getElementById('regimeSelect').addEventListener('change', toggleRegime);
  toggleRegime();
</script>
@endpush

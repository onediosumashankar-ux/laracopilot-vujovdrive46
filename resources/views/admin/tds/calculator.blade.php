@extends('layouts.adminlte')
@section('title', 'TDS Calculator')
@section('page-title', 'TDS Calculator')
@section('breadcrumb', 'Calculator')
@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>TDS Calculator – FY 2024-25</h3>
      </div>
      <form action="{{ route('admin.tds.calculate') }}" method="POST">
        @csrf
        <div class="card-body">

          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-bold">Employee *</label>
              <select name="employee_id" class="form-control" required>
                <option value="">-- Select Employee --</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }} – ₹{{ number_format($emp->salary, 0) }}/yr</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Tax Regime *</label>
              <select name="tax_regime" class="form-control" id="regimeSelect" required>
                <option value="new">New Regime (115BAC)</option>
                <option value="old">Old Regime</option>
              </select>
            </div>
            <div class="col-md-3 form-group">
              <label class="font-weight-bold">Category</label>
              <select name="is_senior_citizen" class="form-control">
                <option value="0">General (Below 60)</option>
                <option value="1">Senior Citizen (60–80)</option>
              </select>
              <input type="hidden" name="is_super_senior" value="0">
            </div>
          </div>

          <!-- OLD REGIME SECTION -->
          <div id="oldRegimeSection" style="display:none">
            <div class="alert alert-success p-2 mb-3">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Old Regime:</strong> You can claim HRA, 80C, 80D and other exemptions/deductions.
            </div>

            <h6 class="font-weight-bold text-success"><i class="fas fa-home mr-1"></i>HRA Exemption</h6>
            <div class="row">
              <div class="col-md-4 form-group">
                <label>HRA Received (Annual) ₹</label>
                <input type="number" name="hra_actual" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>Rent Paid (Annual) ₹</label>
                <input type="number" name="rent_paid_annual" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>City Type</label>
                <select name="metro_city" class="form-control">
                  <option value="0">Non-Metro (40%)</option>
                  <option value="1">Metro – Mumbai/Delhi/Kolkata/Chennai (50%)</option>
                </select>
              </div>
            </div>

            <h6 class="font-weight-bold text-success mt-2"><i class="fas fa-minus-circle mr-1"></i>Chapter VI-A Deductions</h6>
            <div class="row">
              <div class="col-md-4 form-group">
                <label>80C – PF/LIC/ELSS ₹ <small class="text-muted">(max 1,50,000)</small></label>
                <input type="number" name="section_80c" class="form-control" placeholder="0" max="150000">
              </div>
              <div class="col-md-4 form-group">
                <label>80CCD(1B) – NPS ₹ <small class="text-muted">(max 50,000)</small></label>
                <input type="number" name="section_80ccd1b" class="form-control" placeholder="0" max="50000">
              </div>
              <div class="col-md-4 form-group">
                <label>80D – Health Insurance ₹</label>
                <input type="number" name="section_80d" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>80E – Education Loan Interest ₹</label>
                <input type="number" name="section_80e" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>24B – Home Loan Interest ₹ <small class="text-muted">(max 2,00,000)</small></label>
                <input type="number" name="section_24b" class="form-control" placeholder="0" max="200000">
              </div>
              <div class="col-md-4 form-group">
                <label>80G – Donations ₹</label>
                <input type="number" name="section_80g" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>80TTA – Savings Interest ₹ <small class="text-muted">(max 10,000)</small></label>
                <input type="number" name="section_80tta" class="form-control" placeholder="0" max="10000">
              </div>
              <div class="col-md-4 form-group">
                <label>LTA Exemption ₹</label>
                <input type="number" name="lta_exemption" class="form-control" placeholder="0">
              </div>
              <div class="col-md-4 form-group">
                <label>Other Deductions ₹</label>
                <input type="number" name="other_deductions" class="form-control" placeholder="0">
              </div>
            </div>
          </div>

          <!-- NEW REGIME NOTE -->
          <div id="newRegimeNote">
            <div class="alert alert-primary p-2">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>New Regime (Default):</strong> Lower tax slabs. Only ₹50,000 standard deduction applies. No 80C/80D exemptions. Rebate u/s 87A: if income ≤ ₹7L, zero tax.
            </div>
          </div>

        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-calculator mr-1"></i>Calculate TDS
          </button>
          <a href="{{ route('admin.tds.index') }}" class="btn btn-default ml-2">Back</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title"><i class="fas fa-info-circle mr-1"></i>Tax Slabs FY 2024-25</h3></div>
      <div class="card-body p-2">
        <h6 class="font-weight-bold text-primary">New Regime</h6>
        <table class="table table-sm table-bordered mb-3">
          <tr class="thead-light"><th>Income</th><th>Rate</th></tr>
          <tr><td>Up to ₹3L</td><td>0%</td></tr>
          <tr><td>₹3L – ₹6L</td><td>5%</td></tr>
          <tr><td>₹6L – ₹9L</td><td>10%</td></tr>
          <tr><td>₹9L – ₹12L</td><td>15%</td></tr>
          <tr><td>₹12L – ₹15L</td><td>20%</td></tr>
          <tr><td>Above ₹15L</td><td>30%</td></tr>
        </table>
        <h6 class="font-weight-bold text-success">Old Regime</h6>
        <table class="table table-sm table-bordered mb-3">
          <tr class="thead-light"><th>Income</th><th>Rate</th></tr>
          <tr><td>Up to ₹2.5L</td><td>0%</td></tr>
          <tr><td>₹2.5L – ₹5L</td><td>5%</td></tr>
          <tr><td>₹5L – ₹10L</td><td>20%</td></tr>
          <tr><td>Above ₹10L</td><td>30%</td></tr>
        </table>
        <div class="small text-muted">
          <strong>+4% Health &amp; Education Cess</strong> on income tax.<br>
          <strong>Surcharge:</strong> 10% on income >₹50L, 15% >₹1Cr.<br>
          <strong>Rebate 87A:</strong> New: ₹25K if income ≤₹7L | Old: ₹12.5K if ≤₹5L.
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header bg-warning"><h3 class="card-title"><i class="fas fa-shield-alt mr-1"></i>Statutory Deductions</h3></div>
      <div class="card-body p-2 small">
        <table class="table table-sm mb-0">
          <tr><td><strong>PF (Employee)</strong></td><td>12% of Basic (max ₹1,800/mo)</td></tr>
          <tr><td><strong>PF (Employer)</strong></td><td>12% of Basic (max ₹1,800/mo)</td></tr>
          <tr><td><strong>ESI (Employee)</strong></td><td>0.75% if gross ≤₹21,000</td></tr>
          <tr><td><strong>ESI (Employer)</strong></td><td>3.25% if gross ≤₹21,000</td></tr>
          <tr><td><strong>Prof. Tax</strong></td><td>₹0/₹175/₹200 per month</td></tr>
          <tr><td><strong>Std. Deduction</strong></td><td>₹50,000 (both regimes)</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  document.getElementById('regimeSelect').addEventListener('change', function() {
    const isOld = this.value === 'old';
    document.getElementById('oldRegimeSection').style.display = isOld ? 'block' : 'none';
    document.getElementById('newRegimeNote').style.display = isOld ? 'none' : 'block';
  });
</script>
@endpush

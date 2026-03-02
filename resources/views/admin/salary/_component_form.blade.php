<div class="row">
  <div class="col-md-6 form-group">
    <label class="font-weight-bold">Component Name * <small class="text-muted">(e.g. Basic Salary)</small></label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Basic Salary" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Code * <small class="text-muted">(unique key)</small></label>
    <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="BASIC" style="text-transform:uppercase" required>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Component Type *</label>
    <select name="type" class="form-control" required>
      <option value="earning">Earning</option>
      <option value="deduction">Deduction</option>
      <option value="employer_contribution">Employer Contribution</option>
    </select>
  </div>
  <div class="col-md-5 form-group">
    <label class="font-weight-bold">Calculation Method *</label>
    <select name="calculation_type" class="form-control" id="calcTypeSelect{{ isset($edit) ? '_edit' : '' }}" required>
      <option value="fixed">Fixed Amount (per month)</option>
      <option value="percentage_ctc">% of Annual CTC</option>
      <option value="percentage_basic">% of Basic</option>
      <option value="percentage_gross">% of Gross</option>
      <option value="formula">Custom Formula</option>
    </select>
  </div>
  <div class="col-md-4 form-group">
    <label class="font-weight-bold">Value * <small class="text-muted">(amount or %)</small></label>
    <div class="input-group">
      <div class="input-group-prepend"><span class="input-group-text" id="valuePrefix{{ isset($edit) ? '_e' : '' }}">₹</span></div>
      <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', 0) }}" required>
    </div>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Max Monthly Limit ₹</label>
    <input type="number" name="max_limit" class="form-control" value="{{ old('max_limit') }}" placeholder="Leave blank for no limit">
  </div>
  <div class="col-md-12 form-group" id="formulaRow{{ isset($edit) ? '_e' : '' }}" style="display:none">
    <label class="font-weight-bold">Formula <small class="text-muted">(use: ctc, basic, hra, gross)</small></label>
    <input type="text" name="formula" class="form-control" value="{{ old('formula') }}" placeholder="basic * 0.4">
    <small class="text-muted">Example: <code>basic * 0.4</code> | <code>ctc * 0.02</code> | <code>(basic + hra) * 0.1</code></small>
  </div>
  <div class="col-md-3 form-group">
    <label class="font-weight-bold">Sort Order</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
  </div>
  <div class="col-md-9 form-group">
    <label class="font-weight-bold d-block">Flags</label>
    <div class="d-flex flex-wrap">
      <div class="icheck-primary mr-4"><input type="checkbox" name="taxable" id="taxable{{ isset($edit) ? '_e' : '' }}" value="1" checked><label for="taxable{{ isset($edit) ? '_e' : '' }}">Taxable</label></div>
      <div class="icheck-info mr-4"><input type="checkbox" name="pf_applicable" id="pf{{ isset($edit) ? '_e' : '' }}" value="1"><label for="pf{{ isset($edit) ? '_e' : '' }}">PF Applicable</label></div>
      <div class="icheck-warning mr-4"><input type="checkbox" name="esi_applicable" id="esi{{ isset($edit) ? '_e' : '' }}" value="1"><label for="esi{{ isset($edit) ? '_e' : '' }}">ESI Applicable</label></div>
      @isset($edit)<div class="icheck-success"><input type="checkbox" name="is_active" id="active_e" value="1" checked><label for="active_e">Active</label></div>@endisset
    </div>
  </div>
</div>
@push('scripts')
<script>
(function(){
  const sel = document.getElementById('calcTypeSelect{{ isset($edit) ? "_edit" : "" }}');
  const pfx = document.getElementById('valuePrefix{{ isset($edit) ? "_e" : "" }}');
  const row = document.getElementById('formulaRow{{ isset($edit) ? "_e" : "" }}');
  if(!sel) return;
  sel.addEventListener('change', function(){
    const isFixed = this.value === 'fixed';
    const isFml   = this.value === 'formula';
    if(pfx) pfx.textContent = isFixed ? '₹' : '%';
    if(row) row.style.display = isFml ? 'block' : 'none';
  });
})();
</script>
@endpush

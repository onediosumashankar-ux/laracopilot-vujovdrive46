@extends('layouts.adminlte')
@section('title', $structure->name)
@section('page-title', $structure->name)
@section('breadcrumb','Structure Detail')
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-body">
        <h5 class="font-weight-bold">{{ $structure->name }}</h5>
        @if($structure->code)<code>{{ $structure->code }}</code>@endif
        <p class="text-muted small mt-1">{{ $structure->description }}</p>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted">Base CTC</td><td><strong>₹{{ number_format($structure->ctc_amount, 0) }} / {{ $structure->type }}</strong></td></tr>
          <tr><td class="text-muted">Components</td><td>{{ $structure->components->count() }}</td></tr>
          <tr><td class="text-muted">Assigned</td><td>{{ $assignments->count() }} employees</td></tr>
          <tr><td class="text-muted">Status</td><td><span class="badge badge-{{ $structure->is_active ? 'success' : 'secondary' }}">{{ $structure->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
        </table>
        <a href="{{ route('admin.salary.edit', $structure->id) }}" class="btn btn-warning btn-block"><i class="fas fa-edit mr-1"></i>Edit Structure</a>
      </div>
    </div>
    @if($preview)
    <div class="card">
      <div class="card-header bg-dark text-white"><h3 class="card-title"><i class="fas fa-calculator mr-1"></i>CTC Preview</h3></div>
      <div class="card-body p-2">
        <table class="table table-sm mb-0">
          <tr><td class="text-muted">Annual CTC</td><td class="text-right">₹{{ number_format($preview['ctc_annual'], 0) }}</td></tr>
          <tr><td class="text-muted">Monthly CTC</td><td class="text-right">₹{{ number_format($preview['ctc_monthly'], 0) }}</td></tr>
          <tr><td class="text-success">Gross Monthly</td><td class="text-right text-success">₹{{ number_format($preview['gross_monthly'], 0) }}</td></tr>
          <tr><td class="text-danger">Deductions/mo</td><td class="text-right text-danger">₹{{ number_format($preview['total_deductions_monthly'], 0) }}</td></tr>
          <tr class="table-dark"><td><strong>Net Monthly</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_monthly'], 0) }}</strong></td></tr>
          <tr><td class="text-muted">Taxable Annual</td><td class="text-right">₹{{ number_format($preview['taxable_annual'], 0) }}</td></tr>
        </table>
      </div>
    </div>
    @endif
  </div>

  <div class="col-md-8">
    <!-- Components Table -->
    <div class="card">
      <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Salary Components</h3>
        <div class="card-tools">
          <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addComponentModal">
            <i class="fas fa-plus mr-1"></i>Add Component
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
          <thead class="thead-dark">
            <tr><th>#</th><th>Name</th><th>Code</th><th>Type</th><th>Calc</th><th>Value</th>
              <th>Monthly</th><th>Annual</th><th>Taxable</th><th>PF</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($structure->components as $i => $c)
            @php
              $monthly = 0;
              if($preview) {
                $row = collect($preview['components'])->firstWhere('code', $c->code);
                $monthly = $row ? $row['monthly_amount'] : 0;
                $annual  = $row ? $row['annual_amount']  : 0;
              }
            @endphp
            <tr class="{{ !$c->is_active ? 'text-muted' : '' }}">
              <td>{{ $i + 1 }}</td>
              <td><strong>{{ $c->name }}</strong></td>
              <td><code>{{ $c->code }}</code></td>
              <td>
                <span class="badge badge-{{ $c->type === 'earning' ? 'success' : ($c->type === 'deduction' ? 'danger' : 'info') }}">
                  {{ ucfirst(str_replace('_',' ',$c->type)) }}
                </span>
              </td>
              <td><small>{{ ucfirst(str_replace('_',' ',$c->calculation_type)) }}</small></td>
              <td>{{ $c->calculation_type === 'fixed' ? '₹'.number_format($c->value,0) : $c->value.'%' }}</td>
              <td class="{{ $c->type === 'earning' ? 'text-success' : 'text-danger' }}">
                {{ $preview ? '₹'.number_format($monthly,0) : '—' }}
              </td>
              <td class="{{ $c->type === 'earning' ? 'text-success' : 'text-danger' }}">
                {{ $preview ? '₹'.number_format($annual ?? $monthly*12,0) : '—' }}
              </td>
              <td>{{ $c->taxable ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-secondary">No</span>' }}</td>
              <td>{{ $c->pf_applicable ? '<span class="badge badge-info">Yes</span>' : '—' }}</td>
              <td>
                <button class="btn btn-xs btn-warning edit-comp-btn"
                  data-id="{{ $c->id }}"
                  data-name="{{ $c->name }}"
                  data-code="{{ $c->code }}"
                  data-type="{{ $c->type }}"
                  data-calculation_type="{{ $c->calculation_type }}"
                  data-value="{{ $c->value }}"
                  data-formula="{{ $c->formula }}"
                  data-taxable="{{ $c->taxable ? 1 : 0 }}"
                  data-pf_applicable="{{ $c->pf_applicable ? 1 : 0 }}"
                  data-esi_applicable="{{ $c->esi_applicable ? 1 : 0 }}"
                  data-max_limit="{{ $c->max_limit }}"
                  data-sort_order="{{ $c->sort_order }}"
                  data-is_active="{{ $c->is_active ? 1 : 0 }}"
                  data-toggle="modal" data-target="#editComponentModal">
                  <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('admin.salary.components.destroy', [$structure->id, $c->id]) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" onclick="return confirm('Remove?')"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center text-muted py-4">No components yet. Add Basic, HRA, Allowances etc.</td></tr>
            @endforelse
          </tbody>
          @if($preview)
          <tfoot class="table-dark">
            <tr>
              <td colspan="6" class="text-right"><strong>Total Earnings</strong></td>
              <td class="text-success"><strong>₹{{ number_format($preview['gross_monthly'],0) }}</strong></td>
              <td class="text-success"><strong>₹{{ number_format($preview['gross_annual'],0) }}</strong></td>
              <td colspan="3"></td>
            </tr>
            <tr>
              <td colspan="6" class="text-right"><strong>Total Deductions</strong></td>
              <td class="text-danger"><strong>-₹{{ number_format($preview['total_deductions_monthly'],0) }}</strong></td>
              <td class="text-danger"><strong>-₹{{ number_format($preview['total_deductions_annual'],0) }}</strong></td>
              <td colspan="3"></td>
            </tr>
            <tr>
              <td colspan="6" class="text-right"><strong>NET Pay</strong></td>
              <td><strong>₹{{ number_format($preview['net_monthly'],0) }}</strong></td>
              <td><strong>₹{{ number_format($preview['net_annual'],0) }}</strong></td>
              <td colspan="3"></td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- Assigned Employees -->
    @if($assignments->isNotEmpty())
    <div class="card mt-3">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-2"></i>Assigned Employees</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead class="thead-light"><tr><th>Employee</th><th>Effective From</th><th>CTC</th><th>Actions</th></tr></thead>
          <tbody>
            @foreach($assignments as $a)
            <tr>
              <td>{{ $a->employee->full_name ?? 'N/A' }}<br><small class="text-muted">{{ $a->employee->department ?? '' }}</small></td>
              <td>{{ $a->effective_from->format('d M Y') }}</td>
              <td>₹{{ number_format($a->effective_ctc, 0) }}</td>
              <td><a href="{{ route('admin.salary.breakdown', $a->id) }}" class="btn btn-xs btn-info"><i class="fas fa-table"></i> Breakdown</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>
</div>

<!-- Add Component Modal -->
<div class="modal fade" id="addComponentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
        <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Add Salary Component</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ route('admin.salary.components.store', $structure->id) }}" method="POST">
        @csrf
        <div class="modal-body">@include('admin.salary._component_form')</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Add Component</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Component Modal -->
<div class="modal fade" id="editComponentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#b8860b,#daa520);color:#fff">
        <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Component</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="editComponentForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">@include('admin.salary._component_form', ['edit'=>true])</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.edit-comp-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const d = this.dataset;
    const form = document.getElementById('editComponentForm');
    form.action = '/admin/salary/{{ $structure->id }}/components/' + d.id;
    ['name','code','type','calculation_type','value','formula','max_limit','sort_order'].forEach(k => {
      const el = form.querySelector('[name='+k+']');
      if(el) el.value = d[k] || '';
    });
    ['taxable','pf_applicable','esi_applicable','is_active'].forEach(k => {
      const el = form.querySelector('[name='+k+']');
      if(el) el.checked = d[k] == '1';
    });
  });
});
</script>
@endpush

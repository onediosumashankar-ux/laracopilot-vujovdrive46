@extends('layouts.adminlte')
@section('title', 'Form 16 / TDS Certificate')
@section('page-title', 'TDS Certificate (Form 16)')
@section('breadcrumb', 'Form 16')
@push('styles')
<style>
  @media print {
    .main-header,.main-sidebar,.content-header,.main-footer,.no-print{display:none!important}
    .content-wrapper{margin:0!important;background:#fff!important}
    .cert-container{box-shadow:none!important}
  }
  .cert-container{max-width:900px;margin:0 auto;background:#fff;border:2px solid #1a2942;border-radius:8px;overflow:hidden}
  .cert-header{background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff;padding:25px 35px}
  .cert-body{padding:25px 35px}
  .cert-footer{background:#1a2942;color:#888;text-align:center;padding:12px;font-size:0.8rem}
  .section-title{background:#f0f4f8;padding:8px 12px;font-weight:700;border-left:4px solid #2d6a9f;margin:15px 0 8px}
</style>
@endpush
@section('content')
<div class="no-print mb-3">
  <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i>Print Form 16</button>
  <a href="{{ route('admin.tds.index') }}" class="btn btn-default ml-2">Back</a>
</div>
<div class="cert-container">
  <div class="cert-header">
    <div class="d-flex justify-content-between">
      <div>
        <h3 class="mb-0">FORM 16 – TDS CERTIFICATE</h3>
        <div style="opacity:.8">Certificate of Tax Deducted at Source under Section 203 of Income Tax Act</div>
      </div>
      <div class="text-right">
        <div style="font-size:1.2rem;font-weight:700">FY {{ $fy }}</div>
        <div style="opacity:.8">{{ $employee->tenant->name ?? 'Company' }}</div>
      </div>
    </div>
  </div>
  <div class="cert-body">
    <div class="row">
      <div class="col-md-6">
        <div class="section-title">Deductor (Employer)</div>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted" style="width:40%">Name</td><td><strong>{{ $employee->tenant->name ?? 'N/A' }}</strong></td></tr>
          <tr><td class="text-muted">Address</td><td>{{ $employee->tenant->address ?? 'N/A' }}</td></tr>
          <tr><td class="text-muted">Email</td><td>{{ $employee->tenant->email ?? 'N/A' }}</td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <div class="section-title">Deductee (Employee)</div>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted" style="width:40%">Name</td><td><strong>{{ $employee->full_name }}</strong></td></tr>
          <tr><td class="text-muted">PAN</td><td>{{ $employee->tax_id ?? 'XXXXXXXXXX' }}</td></tr>
          <tr><td class="text-muted">Designation</td><td>{{ $employee->position }}</td></tr>
          <tr><td class="text-muted">Department</td><td>{{ $employee->department }}</td></tr>
        </table>
      </div>
    </div>

    @if($declaration)
    <div class="section-title">Part A – Summary of TDS Deducted</div>
    <table class="table table-bordered table-sm">
      <thead class="thead-dark"><tr><th>Month</th><th>Gross Salary</th><th>TDS Deducted</th><th>Cess</th><th>Total TDS</th><th>Status</th></tr></thead>
      <tbody>
        @php $months = [1=>'Apr',2=>'May',3=>'Jun',4=>'Jul',5=>'Aug',6=>'Sep',7=>'Oct',8=>'Nov',9=>'Dec',10=>'Jan',11=>'Feb',12=>'Mar']; @endphp
        @foreach($deductions as $ded)
        <tr>
          <td>{{ $months[$ded->month] ?? $ded->month }} {{ $ded->year }}</td>
          <td>₹{{ number_format($ded->gross_salary, 2) }}</td>
          <td>₹{{ number_format($ded->tds_amount, 2) }}</td>
          <td>₹{{ number_format($ded->cess, 2) }}</td>
          <td><strong>₹{{ number_format($ded->total_tds, 2) }}</strong></td>
          <td><span class="badge badge-{{ $ded->status === 'deposited' ? 'success' : ($ded->status === 'deducted' ? 'warning' : 'secondary') }}">{{ ucfirst($ded->status) }}</span></td>
        </tr>
        @endforeach
        <tr class="table-dark"><td colspan="4"><strong>Total TDS Deducted</strong></td><td><strong>₹{{ number_format($totalTdsDeducted, 2) }}</strong></td><td></td></tr>
      </tbody>
    </table>

    <div class="section-title">Part B – Salary & Tax Computation</div>
    <table class="table table-bordered table-sm">
      <tr><td>Gross Annual Salary</td><td class="text-right">₹{{ number_format($declaration->gross_annual_income, 2) }}</td></tr>
      <tr><td>(-) Standard Deduction</td><td class="text-right text-success">-₹50,000.00</td></tr>
      @if($declaration->total_exemptions > 0)
      <tr><td>(-) Exemptions (HRA/LTA)</td><td class="text-right text-success">-₹{{ number_format($declaration->total_exemptions, 2) }}</td></tr>
      @endif
      @if($declaration->total_deductions > 0)
      <tr><td>(-) Chapter VI-A Deductions</td><td class="text-right text-success">-₹{{ number_format($declaration->total_deductions, 2) }}</td></tr>
      @endif
      <tr class="table-warning"><td><strong>Taxable Income</strong></td><td class="text-right"><strong>₹{{ number_format($declaration->taxable_income, 2) }}</strong></td></tr>
      <tr><td>Income Tax (Slab)</td><td class="text-right">₹{{ number_format($declaration->annual_tax, 2) }}</td></tr>
      @if($declaration->surcharge > 0)<tr><td>Surcharge</td><td class="text-right">₹{{ number_format($declaration->surcharge, 2) }}</td></tr>@endif
      <tr><td>H&E Cess (4%)</td><td class="text-right">₹{{ number_format($declaration->health_education_cess, 2) }}</td></tr>
      <tr class="table-danger"><td><strong>Total Tax Liability</strong></td><td class="text-right"><strong>₹{{ number_format($declaration->total_tax_liability, 2) }}</strong></td></tr>
      <tr><td>TDS Deducted (As per Part A)</td><td class="text-right">₹{{ number_format($totalTdsDeducted, 2) }}</td></tr>
      @php $diff = $declaration->total_tax_liability - $totalTdsDeducted; @endphp
      @if($diff > 0)<tr class="table-warning"><td><strong>Balance Tax Payable</strong></td><td class="text-right text-danger"><strong>₹{{ number_format($diff, 2) }}</strong></td></tr>
      @elseif($diff < 0)<tr class="table-success"><td><strong>TDS Refund Due</strong></td><td class="text-right text-success"><strong>₹{{ number_format(abs($diff), 2) }}</strong></td></tr>@endif
    </table>
    @else
    <div class="alert alert-warning">No TDS declaration found for FY {{ $fy }}. Please complete TDS declaration first.</div>
    @endif

    <div class="row mt-4">
      <div class="col-md-6">
        <div class="border p-3" style="min-height:80px">
          <div class="text-muted small">Signature of Deductor</div>
          <div class="mt-4 pt-2 border-top">Authorized Signatory</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="border p-3" style="min-height:80px">
          <div class="text-muted small">Place & Date</div>
          <div class="mt-4 pt-2 border-top">{{ now()->format('d M Y') }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="cert-footer">
    This is a computer-generated Form 16. FY {{ $fy }} &bull; Generated on {{ now()->format('d M Y h:i A') }} &bull; TalentFlow HRMS
  </div>
</div>
@endsection

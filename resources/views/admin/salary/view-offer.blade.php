@extends('layouts.adminlte')
@section('title','Offer Letter')
@section('page-title','Offer Letter')
@section('breadcrumb','Offer Letter')
@push('styles')
<style>
@media print{.main-header,.main-sidebar,.content-header,.main-footer,.no-print{display:none!important}.content-wrapper{margin:0!important;background:#fff!important}}
.offer-body{max-width:900px;margin:0 auto;background:#fff;border:1px solid #ddd;border-radius:8px;overflow:hidden}
.offer-header{background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff;padding:30px 40px}
.offer-content{padding:30px 40px}
.offer-footer{background:#f8f9fa;padding:20px 40px;border-top:2px solid #1a2942}
</style>
@endpush
@section('content')
<div class="no-print mb-3">
  <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i>Print / PDF</button>
  <a href="{{ route('admin.salary.offers') }}" class="btn btn-default ml-2">Back to List</a>
  @if($offer->status === 'draft')
  <form action="{{ route('admin.salary.offer.send', $offer->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-success ml-2"><i class="fas fa-paper-plane mr-1"></i>Mark as Sent</button>
  </form>
  @endif
  <form action="{{ route('admin.salary.offer.status', $offer->id) }}" method="POST" class="d-inline ml-2">
    @csrf @method('PUT')
    <select name="status" class="form-control d-inline" style="width:140px">
      @foreach(['draft','sent','accepted','rejected','expired'] as $st)
      <option value="{{ $st }}" {{ $offer->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
    <button class="btn btn-warning ml-1">Update</button>
  </form>
</div>

<div class="offer-body">
  <div class="offer-header">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h2 class="mb-1">{{ $offer->tenant->name ?? 'Company Name' }}</h2>
        <div style="opacity:.8">{{ $offer->tenant->address ?? '' }}</div>
        <div style="opacity:.8">{{ $offer->tenant->email ?? '' }}</div>
      </div>
      <div class="text-right">
        <h3>OFFER LETTER</h3>
        <div>Ref: {{ $offer->offer_number }}</div>
        <span class="badge badge-{{ $offer->status === 'accepted' ? 'success' : ($offer->status === 'rejected' ? 'danger' : ($offer->status === 'sent' ? 'info' : 'secondary')) }} mt-1">
          {{ strtoupper($offer->status) }}
        </span>
      </div>
    </div>
  </div>

  <div class="offer-content">
    <p>Date: {{ now()->format('d F Y') }}</p>
    @if($offer->employee)
    <p><strong>To,</strong><br>
    {{ $offer->employee->full_name }}<br>
    {{ $offer->employee->address ?? '' }}
    </p>
    @elseif($offer->candidate)
    <p><strong>To,</strong><br>
    {{ $offer->candidate->full_name }}<br>
    {{ $offer->candidate->email }}
    </p>
    @endif

    <p>Dear <strong>{{ $offer->employee->full_name ?? ($offer->candidate->full_name ?? 'Candidate') }}</strong>,</p>

    <p>We are pleased to extend this offer of employment for the position of <strong>{{ $offer->position }}</strong> in the <strong>{{ $offer->department }}</strong> department at <strong>{{ $offer->tenant->name ?? 'the Company' }}</strong>.</p>

    <h5 class="font-weight-bold mt-4 border-bottom pb-2">Employment Terms</h5>
    <table class="table table-bordered table-sm">
      <tr><td style="width:35%"><strong>Position</strong></td><td>{{ $offer->position }}</td></tr>
      <tr><td><strong>Department</strong></td><td>{{ $offer->department }}</td></tr>
      <tr><td><strong>Employment Type</strong></td><td>{{ ucfirst(str_replace('_',' ',$offer->employment_type)) }}</td></tr>
      <tr><td><strong>Date of Joining</strong></td><td>{{ $offer->joining_date->format('d F Y') }}</td></tr>
      <tr><td><strong>Work Location</strong></td><td>{{ $offer->work_location ?? 'Head Office' }}</td></tr>
      <tr><td><strong>Offer Valid Until</strong></td><td>{{ $offer->offer_expiry->format('d F Y') }}</td></tr>
    </table>

    <h5 class="font-weight-bold mt-4 border-bottom pb-2">Compensation Details</h5>
    @if($preview)
    <table class="table table-bordered table-sm">
      <thead class="thead-light"><tr><th>Component</th><th class="text-right">Monthly (₹)</th><th class="text-right">Annual (₹)</th></tr></thead>
      <tbody>
        @foreach(collect($preview['components'])->where('type','earning') as $r)
        <tr><td>{{ $r['name'] }}</td><td class="text-right">{{ number_format($r['monthly_amount'],2) }}</td><td class="text-right">{{ number_format($r['annual_amount'],2) }}</td></tr>
        @endforeach
        <tr class="table-success"><td><strong>Gross Salary</strong></td><td class="text-right"><strong>{{ number_format($preview['gross_monthly'],2) }}</strong></td><td class="text-right"><strong>{{ number_format($preview['gross_annual'],2) }}</strong></td></tr>
        @if(collect($preview['components'])->where('type','deduction')->isNotEmpty())
        @foreach(collect($preview['components'])->where('type','deduction') as $r)
        <tr><td class="text-danger">(-) {{ $r['name'] }}</td><td class="text-right text-danger">{{ number_format($r['monthly_amount'],2) }}</td><td class="text-right text-danger">{{ number_format($r['annual_amount'],2) }}</td></tr>
        @endforeach
        @endif
        <tr class="table-primary"><td><strong>Net Take-Home</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_monthly'],2) }}</strong></td><td class="text-right"><strong>₹{{ number_format($preview['net_annual'],2) }}</strong></td></tr>
        <tr><td colspan="2"><strong>Annual CTC</strong></td><td class="text-right"><strong>₹{{ number_format($preview['ctc_annual'],2) }}</strong></td></tr>
      </tbody>
    </table>
    @endif

    @if($offer->custom_clauses)
    <h5 class="font-weight-bold mt-4 border-bottom pb-2">Additional Terms & Conditions</h5>
    <div style="white-space:pre-line">{{ $offer->custom_clauses }}</div>
    @endif

    <p class="mt-4">Please confirm your acceptance by signing and returning a copy of this letter on or before <strong>{{ $offer->offer_expiry->format('d F Y') }}</strong>.</p>
    <p>We look forward to welcoming you to our team!</p>

    <div class="row mt-5">
      <div class="col-6">
        <div class="border-top pt-2 text-center">Authorized Signatory<br><small class="text-muted">{{ $offer->tenant->name ?? '' }}</small></div>
      </div>
      <div class="col-6">
        <div class="border-top pt-2 text-center">Candidate Signature &amp; Date<br><small class="text-muted">Acceptance</small></div>
      </div>
    </div>
  </div>

  <div class="offer-footer">
    <small class="text-muted">Offer Number: {{ $offer->offer_number }} &bull; Generated: {{ now()->format('d M Y h:i A') }} &bull; TalentFlow HRMS &bull; {{ $offer->tenant->name ?? '' }}</small>
  </div>
</div>
@endsection

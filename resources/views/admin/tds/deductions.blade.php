@extends('layouts.adminlte')
@section('title', 'TDS Deductions')
@section('page-title', 'Monthly TDS Deduction Records')
@section('breadcrumb', 'Deductions')
@section('content')
<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-list mr-2"></i>TDS Deduction Ledger</h3>
    <div class="card-tools">
      <a href="{{ route('admin.tds.index') }}" class="btn btn-sm btn-light">Back to TDS</a>
    </div>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr><th>Employee</th><th>FY</th><th>Month</th><th>Gross</th><th>TDS</th><th>Cess</th><th>Total TDS</th><th>Status</th><th>Challan</th><th>Update</th></tr>
      </thead>
      <tbody>
        @forelse($deductions as $d)
        <tr>
          <td><strong>{{ $d->employee->full_name ?? 'N/A' }}</strong></td>
          <td>{{ $d->financial_year }}</td>
          <td>{{ \Carbon\Carbon::create(null, $d->month)->format('M') }} {{ $d->year }}</td>
          <td>₹{{ number_format($d->gross_salary, 0) }}</td>
          <td>₹{{ number_format($d->tds_amount, 2) }}</td>
          <td>₹{{ number_format($d->cess, 2) }}</td>
          <td class="text-danger font-weight-bold">₹{{ number_format($d->total_tds, 2) }}</td>
          <td>
            <span class="badge badge-{{ $d->status === 'deposited' ? 'success' : ($d->status === 'deducted' ? 'warning' : 'secondary') }}">{{ ucfirst($d->status) }}</span>
          </td>
          <td>{{ $d->challan_number ?? '—' }}</td>
          <td>
            <form action="{{ route('admin.tds.deductions.update', $d->id) }}" method="POST" class="form-inline">
              @csrf @method('PUT')
              <select name="status" class="form-control form-control-sm mr-1" style="width:100px">
                <option value="pending" {{ $d->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="deducted" {{ $d->status === 'deducted' ? 'selected' : '' }}>Deducted</option>
                <option value="deposited" {{ $d->status === 'deposited' ? 'selected' : '' }}>Deposited</option>
              </select>
              <input type="text" name="challan_number" class="form-control form-control-sm mr-1" style="width:100px" placeholder="Challan#" value="{{ $d->challan_number }}">
              <button class="btn btn-xs btn-success"><i class="fas fa-check"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-5 text-muted"><i class="fas fa-list fa-2x d-block mb-2"></i>No TDS deduction records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $deductions->links() }}</div>
</div>
@endsection

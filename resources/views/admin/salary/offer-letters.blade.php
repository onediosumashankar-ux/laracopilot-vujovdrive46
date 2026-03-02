@extends('layouts.adminlte')
@section('title','Offer Letters')
@section('page-title','Offer Letters')
@section('breadcrumb','Offer Letters')
@section('content')
<div class="mb-3">
  <a href="{{ route('admin.salary.offer.create') }}" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Create Offer Letter</a>
  <a href="{{ route('admin.salary.index') }}" class="btn btn-default ml-2">Salary Structures</a>
</div>
<div class="card">
  <div class="card-header" style="background:linear-gradient(135deg,#1a2942,#2d6a9f);color:#fff">
    <h3 class="card-title"><i class="fas fa-file-contract mr-2"></i>All Offer Letters</h3>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-sm mb-0">
      <thead class="thead-dark">
        <tr><th>Offer#</th><th>Recipient</th><th>Position</th><th>Joining</th><th>Annual CTC</th><th>Expiry</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($offers as $o)
        <tr>
          <td><code>{{ $o->offer_number }}</code></td>
          <td>
            {{ $o->employee->full_name ?? ($o->candidate->full_name ?? 'N/A') }}
            <br><small class="text-muted">{{ $o->employee ? 'Employee' : 'Candidate' }}</small>
          </td>
          <td>{{ $o->position }}<br><small class="text-muted">{{ $o->department }}</small></td>
          <td>{{ $o->joining_date->format('d M Y') }}</td>
          <td>₹{{ number_format($o->ctc_annual, 0) }}</td>
          <td>
            {{ $o->offer_expiry->format('d M Y') }}
            @if($o->offer_expiry->isPast() && !in_array($o->status, ['accepted','rejected']))
            <span class="badge badge-danger">Expired</span>
            @endif
          </td>
          <td>
            <span class="badge badge-{{ $o->status === 'accepted' ? 'success' : ($o->status === 'rejected' ? 'danger' : ($o->status === 'sent' ? 'info' : ($o->status === 'expired' ? 'secondary' : 'warning'))} }}">
              {{ ucfirst($o->status) }}
            </span>
          </td>
          <td>
            <a href="{{ route('admin.salary.offer.view', $o->id) }}" class="btn btn-xs btn-primary"><i class="fas fa-eye"></i></a>
            @if($o->status === 'draft')
            <form action="{{ route('admin.salary.offer.send', $o->id) }}" method="POST" class="d-inline">
              @csrf
              <button class="btn btn-xs btn-info" title="Mark Sent"><i class="fas fa-paper-plane"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-5 text-muted">
          <i class="fas fa-file-contract fa-2x d-block mb-2"></i>No offer letters yet.
          <a href="{{ route('admin.salary.offer.create') }}">Create the first one</a>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $offers->links() }}</div>
</div>
@endsection

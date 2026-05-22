@extends('biometric::layouts.app')
@section('title','Leave Requests')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <form class="form-inline" method="GET">
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">All Status</option>
                @foreach(['pending','approved','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary mr-2">Filter</button>
            <a href="{{ route('biometric.leave.types') }}" class="btn btn-sm btn-outline-info">Leave Types</a>
        </form>
    </div>
    <a href="{{ route('biometric.leave.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i>Apply Leave</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="thead-light"><tr>
                <th>Employee</th><th>Type</th><th>From</th><th>To</th>
                <th>Days</th><th>Reason</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
            @forelse($requests as $r)
            @php
                $badge = match($r->status) {'pending'=>'warning','approved'=>'success','rejected'=>'danger',default=>'secondary'};
            @endphp
            <tr>
                <td><strong>{{ $r->employee?->name }}</strong><br>
                    <small class="text-muted">{{ $r->employee?->employee_code }}</small></td>
                <td><span class="badge badge-light">{{ $r->leaveType?->name }}</span>
                    @if($r->leaveType?->is_paid)<small class="text-success ml-1">Paid</small>@endif</td>
                <td>{{ $r->from_date->format('d M Y') }}</td>
                <td>{{ $r->to_date->format('d M Y') }}</td>
                <td><strong>{{ $r->days }}</strong></td>
                <td><small>{{ Str::limit($r->reason, 40) }}</small></td>
                <td><span class="badge badge-{{ $badge }}">{{ ucfirst($r->status) }}</span></td>
                <td>
                    @if($r->status === 'pending')
                    <form method="POST" action="{{ route('biometric.leave.approve',$r->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-success">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('biometric.leave.reject',$r->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-danger">Reject</button>
                    </form>
                    @else
                    <small class="text-muted">{{ $r->approved_by }}</small>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No leave requests found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-white">{{ $requests->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

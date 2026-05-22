@extends('biometric::layouts.app')
@section('title','Raw Punches')
@section('content')
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="form-inline" method="GET">
            <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ request('date', now()->toDateString()) }}">
            <button class="btn btn-sm btn-outline-secondary">Filter</button>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <span class="font-weight-bold"><i class="fas fa-list mr-2"></i>Raw Punch Records</span>
        <small class="text-muted ml-2">— received from offline agent — no processing applied</small>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="thead-light"><tr>
                <th>Employee</th><th>Punched At</th><th>Type</th><th>Verify</th><th>Device SN</th><th>Source</th>
            </tr></thead>
            <tbody>
            @forelse($punches as $p)
            <tr>
                <td>{{ $p->employee?->name ?? $p->zk_user_id }}<br>
                    <small class="text-muted">{{ $p->employee?->employee_code }}</small></td>
                <td><strong>{{ $p->punched_at->format('Y-m-d H:i:s') }}</strong></td>
                <td><span class="badge badge-{{ $p->punch_type=='check_in'?'success':($p->punch_type=='check_out'?'secondary':'light') }}">
                    {{ $p->punch_type }}</span></td>
                <td><span class="badge badge-light">{{ $p->verify_type }}</span></td>
                <td><small>{{ $p->device_sn }}</small></td>
                <td><small class="text-muted">{{ $p->source }}</small></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No punches found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($punches->hasPages())
    <div class="card-footer bg-white">{{ $punches->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

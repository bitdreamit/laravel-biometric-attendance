@extends('biometric::layouts.app')
@section('title','Dashboard')
@section('content')
<div class="row mb-4">
    @foreach([
        ['label'=>'Total Employees','value'=>$stats['total_employees'],'icon'=>'users','color'=>'primary'],
        ['label'=>'Present Today','value'=>$stats['present_today'],'icon'=>'check-circle','color'=>'success'],
        ['label'=>'Absent Today','value'=>$stats['absent_today'],'icon'=>'times-circle','color'=>'danger'],
        ['label'=>'Late Today','value'=>$stats['late_today'],'icon'=>'clock','color'=>'warning'],
        ['label'=>'Total Devices','value'=>$stats['total_devices'],'icon'=>'tablet-alt','color'=>'info'],
        ['label'=>'Punches Today','value'=>$stats['punches_today'],'icon'=>'fingerprint','color'=>'secondary'],
    ] as $s)
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body text-center py-3">
                <i class="fas fa-{{ $s['icon'] }} fa-2x text-{{ $s['color'] }} mb-2"></i>
                <div class="h4 mb-0 font-weight-bold">{{ $s['value'] }}</div>
                <div class="text-muted small">{{ $s['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="font-weight-bold"><i class="fas fa-list mr-2"></i>Recent Punches Today</span>
        <a href="{{ route('biometric.attendance.punches') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr>
                    <th>Employee</th><th>Time</th><th>Type</th><th>Verify</th><th>Device</th>
                </tr></thead>
                <tbody>
                @forelse($recent_punches as $p)
                <tr>
                    <td><strong>{{ $p->employee?->name ?? $p->zk_user_id }}</strong><br><small class="text-muted">{{ $p->employee?->employee_code }}</small></td>
                    <td>{{ $p->punched_at->format('H:i:s') }}</td>
                    <td><span class="badge badge-{{ $p->punch_type=='check_in' ? 'success':'secondary' }}">{{ $p->punch_type }}</span></td>
                    <td><span class="badge badge-light">{{ $p->verify_type }}</span></td>
                    <td><small>{{ $p->device_sn }}</small></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No punches today yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

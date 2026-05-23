@extends('biometric::layouts.app')
@section('title','Dashboard')
@section('content')

{{-- Agent status banner --}}
@if(isset($agent_status))
<div class="alert alert-{{ $agent_status['online'] ? 'success' : 'warning' }} d-flex align-items-center mb-3 py-2">
    <i class="fas fa-{{ $agent_status['online'] ? 'circle text-success' : 'exclamation-triangle text-warning' }} mr-2"></i>
    @if($agent_status['online'])
        <span>Agent <strong>online</strong> — last seen {{ $agent_status['last_seen'] }}
            &nbsp;|&nbsp; v{{ $agent_status['version'] }}
            &nbsp;|&nbsp; Queue: {{ $agent_status['queue']['pending'] ?? 0 }} pending,
            {{ $agent_status['queue']['error'] ?? 0 }} errors</span>
    @else
        <span>Agent status <strong>unknown</strong> — no heartbeat received in the last 15 minutes.
            Check that the offline agent is running and <code>server.url</code> is configured.</span>
    @endif
    <a href="{{ route('biometric.devices.index') }}" class="ml-auto btn btn-sm btn-outline-secondary">Devices</a>
</div>
@endif

{{-- Stats row --}}
<div class="row mb-4">
    @foreach([
        ['label'=>'Total Employees','value'=>$stats['total_employees'],'icon'=>'users','color'=>'primary'],
        ['label'=>'Present Today',  'value'=>$stats['present_today'],  'icon'=>'check-circle','color'=>'success'],
        ['label'=>'Absent Today',   'value'=>$stats['absent_today'],   'icon'=>'times-circle','color'=>'danger'],
        ['label'=>'Late Today',     'value'=>$stats['late_today'],     'icon'=>'clock','color'=>'warning'],
        ['label'=>'Active Devices', 'value'=>$stats['total_devices'],  'icon'=>'tablet-alt','color'=>'info'],
        ['label'=>'Punches Today',  'value'=>$stats['punches_today'],  'icon'=>'fingerprint','color'=>'secondary'],
    ] as $s)
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <i class="fas fa-{{ $s['icon'] }} fa-2x text-{{ $s['color'] }} mb-2"></i>
                <div class="h4 mb-0 font-weight-bold">{{ $s['value'] }}</div>
                <div class="text-muted small">{{ $s['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row">
    {{-- Recent punches --}}
    <div class="col-md-8 mb-3">
        <div class="card shadow-sm h-100">
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
                            <td><strong>{{ $p->employee?->name ?? $p->zk_user_id }}</strong>
                                <br><small class="text-muted">{{ $p->employee?->employee_code }}</small></td>
                            <td>{{ $p->punched_at->format('H:i:s') }}</td>
                            <td><span class="badge badge-{{ $p->punch_type=='check_in'?'success':'secondary' }}">{{ $p->punch_type }}</span></td>
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
    </div>

    {{-- Device status panel --}}
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-tablet-alt mr-2"></i>Device Status
            </div>
            <div class="card-body p-0">
                @forelse($devices as $device)
                @php
                    $online = $device->last_seen_at && $device->last_seen_at->gt(now()->subMinutes(15));
                @endphp
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <span class="fa-stack fa-xs mr-2">
                        <i class="fas fa-circle fa-stack-2x text-{{ $online ? 'success' : 'secondary' }}" style="opacity:.15"></i>
                        <i class="fas fa-tablet-alt fa-stack-1x text-{{ $online ? 'success' : 'secondary' }}"></i>
                    </span>
                    <div class="flex-grow-1 small">
                        <strong>{{ $device->name }}</strong>
                        <br><span class="text-muted">{{ $device->serial_number }}</span>
                    </div>
                    <div class="text-right small">
                        <span class="badge badge-{{ $online ? 'success' : 'secondary' }}">
                            {{ $online ? 'Online' : 'Offline' }}
                        </span>
                        <br><span class="text-muted">{{ $device->last_seen_at?->diffForHumans() ?? 'Never' }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">
                    No devices added.<br>
                    <a href="{{ route('biometric.devices.create') }}">Add a device</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

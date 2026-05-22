@extends('biometric::layouts.app')
@section('title','Devices')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 font-weight-bold">ZKTeco Devices</h6>
    <a href="{{ route('biometric.devices.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i>Add Device</a>
</div>
<div class="row">
@forelse($devices as $d)
<div class="col-md-4 mb-3">
    <div class="card shadow-sm h-100 {{ !$d->is_active ? 'border-secondary' : '' }}">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h6 class="card-title mb-1">{{ $d->name }}</h6>
                <span class="badge badge-{{ $d->is_active ? 'success' : 'secondary' }}">
                    {{ $d->is_active ? 'Active' : 'Disabled' }}
                </span>
            </div>
            <small class="text-muted d-block mb-2">{{ $d->location }}</small>
            <table class="table table-sm table-borderless mb-2 small">
                <tr><th class="pl-0">Serial</th><td>{{ $d->serial_number }}</td></tr>
                <tr><th class="pl-0">IP</th><td>{{ $d->ip }}:{{ $d->port }}</td></tr>
                <tr><th class="pl-0">Last Seen</th><td>{{ $d->last_seen_at?->diffForHumans() ?? 'Never' }}</td></tr>
                <tr><th class="pl-0">Punches Today</th><td><span class="badge badge-info">{{ $d->today_punches }}</span></td></tr>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between">
            <a href="{{ route('biometric.devices.edit',$d->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('biometric.devices.destroy',$d->id) }}"
                  onsubmit="return confirm('Disable this device?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Disable</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="card shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-tablet-alt fa-3x mb-3"></i><br>
            No devices added yet. <a href="{{ route('biometric.devices.create') }}">Add your first device</a>.
        </div>
    </div>
</div>
@endforelse
</div>
@endsection

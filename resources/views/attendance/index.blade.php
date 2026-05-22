@extends('biometric::layouts.app')
@section('title','Daily Attendance Log')
@section('content')
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="form-inline" method="GET">
            <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ $date }}">
            <select name="department" class="form-control form-control-sm mr-2">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                <option value="{{ $d }}" {{ request('department')==$d?'selected':'' }}>{{ $d }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">All Status</option>
                @foreach($statuses as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary mr-2">Filter</button>
        </form>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-2">
    <span class="font-weight-bold">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</span>
    <div>
        <form method="POST" action="{{ route('biometric.attendance.process') }}" class="d-inline">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <button class="btn btn-sm btn-outline-info" onclick="return confirm('Process attendance for {{ $date }}?')">
                <i class="fas fa-sync mr-1"></i>Process
            </button>
        </form>
        <a href="{{ route('biometric.reports.export', ['type'=>'daily','format'=>'csv','date'=>$date]) }}"
           class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-download mr-1"></i>CSV</a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr>
                    <th>Code</th><th>Name</th><th>Shift</th><th>Check In</th><th>Check Out</th>
                    <th>Hours</th><th>Late</th><th>OT</th><th>Status</th>
                </tr></thead>
                <tbody>
                @forelse($logs as $log)
                @php
                    $badge = match($log->status) {
                        'present'  => 'success',
                        'absent'   => 'danger',
                        'late'     => 'warning',
                        'on_leave' => 'primary',
                        'half_day' => 'warning',
                        default    => 'secondary'
                    };
                @endphp
                <tr>
                    <td><strong>{{ $log->employee?->employee_code }}</strong></td>
                    <td>{{ $log->employee?->name }}<br><small class="text-muted">{{ $log->employee?->department }}</small></td>
                    <td><small>{{ $log->shift?->name ?? '—' }}</small></td>
                    <td>{{ $log->check_in?->format('H:i') ?? '—' }}</td>
                    <td>{{ $log->check_out?->format('H:i') ?? '—' }}</td>
                    <td>{{ $log->workingHours() }}</td>
                    <td>{{ $log->late_minutes > 0 ? $log->late_minutes.'m' : '—' }}</td>
                    <td>{{ $log->overtime_minutes > 0 ? $log->overtime_minutes.'m' : '—' }}</td>
                    <td><span class="badge badge-{{ $badge }}">{{ ucfirst(str_replace('_',' ',$log->status)) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">
                    No records for this date. Click <strong>Process</strong> to calculate attendance.
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

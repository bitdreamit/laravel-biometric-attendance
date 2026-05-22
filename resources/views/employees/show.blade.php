@extends('biometric::layouts.app')
@section('title', $employee->name)
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:70px;height:70px">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
                <h5 class="mb-0">{{ $employee->name }}</h5>
                <small class="text-muted">{{ $employee->designation }}</small>
                <div class="mt-2">
                    <span class="badge badge-{{ $employee->status=='active'?'success':'secondary' }}">{{ $employee->status }}</span>
                    <span class="badge badge-{{ $employee->sync_status=='synced'?'info':'warning' }} ml-1">{{ $employee->sync_status }}</span>
                </div>
            </div>
            <div class="card-footer bg-white">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><th>Code</th><td>{{ $employee->employee_code }}</td></tr>
                    <tr><th>Department</th><td>{{ $employee->department ?? '—' }}</td></tr>
                    <tr><th>Email</th><td>{{ $employee->email ?? '—' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $employee->phone ?? '—' }}</td></tr>
                    <tr><th>Join Date</th><td>{{ $employee->join_date?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><th>ZK User ID</th><td>{{ $employee->zk_user_id ?? '—' }}</td></tr>
                    <tr><th>Card Number</th><td>{{ $employee->card_number ?? '—' }}</td></tr>
                    <tr><th>Current Shift</th><td>{{ $employee->currentShift()?->name ?? 'Not assigned' }}</td></tr>
                </table>
                <a href="{{ route('biometric.employees.edit',$employee->id) }}" class="btn btn-sm btn-outline-secondary btn-block mt-2">Edit</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        {{-- Recent attendance --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white font-weight-bold">Recent Attendance (Last 30 Days)</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light"><tr>
                        <th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Late</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                    @forelse($employee->attendanceLogs as $log)
                    @php $b = ['present'=>'success','absent'=>'danger','on_leave'=>'primary','half_day'=>'warning'][$log->status]??'secondary'; @endphp
                    <tr>
                        <td>{{ $log->work_date->format('d M') }}</td>
                        <td>{{ $log->check_in?->format('H:i') ?? '—' }}</td>
                        <td>{{ $log->check_out?->format('H:i') ?? '—' }}</td>
                        <td>{{ $log->workingHours() }}</td>
                        <td>{{ $log->late_minutes > 0 ? $log->late_minutes.'m' : '—' }}</td>
                        <td><span class="badge badge-{{ $b }}">{{ ucfirst(str_replace('_',' ',$log->status)) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No attendance records.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Leave requests --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="font-weight-bold">Leave Requests</span>
                <a href="{{ route('biometric.leave.create') }}?employee_id={{ $employee->id }}" class="btn btn-xs btn-outline-primary">Apply Leave</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light"><tr>
                        <th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                    @forelse($employee->leaveRequests as $lr)
                    @php $b = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$lr->status]; @endphp
                    <tr>
                        <td>{{ $lr->leaveType?->name }}</td>
                        <td>{{ $lr->from_date->format('d M Y') }}</td>
                        <td>{{ $lr->to_date->format('d M Y') }}</td>
                        <td>{{ $lr->days }}</td>
                        <td><span class="badge badge-{{ $b }}">{{ ucfirst($lr->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No leave requests.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('biometric::layouts.app')
@section('title','Monthly Report')
@section('content')
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="form-inline" method="GET">
            <select name="year" class="form-control form-control-sm mr-2">
                @for($y=now()->year; $y>=now()->year-3; $y--)
                <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="form-control form-control-sm mr-2">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary">View</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white font-weight-bold">
        Monthly Report — {{ \Carbon\Carbon::create($year,$month)->format('F Y') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr>
                    <th>Code</th><th>Name</th><th>Department</th>
                    <th>Present</th><th>Absent</th><th>Leave</th><th>Late Days</th>
                    <th>Total Hours</th><th>OT Hours</th>
                </tr></thead>
                <tbody>
                @forelse($employees as $emp)
                @php
                    $logs = $emp->attendanceLogs;
                    $present  = $logs->where('status','present')->count();
                    $absent   = $logs->where('status','absent')->count();
                    $leave    = $logs->where('status','on_leave')->count();
                    $lateDays = $logs->where('late_minutes','>',0)->count();
                    $totalMin = $logs->sum('working_minutes');
                    $otMin    = $logs->sum('overtime_minutes');
                    $totalHrs = floor($totalMin/60).'h '.($totalMin%60).'m';
                    $otHrs    = floor($otMin/60).'h '.($otMin%60).'m';
                @endphp
                <tr>
                    <td>{{ $emp->employee_code }}</td>
                    <td>{{ $emp->name }}</td>
                    <td><small>{{ $emp->department }}</small></td>
                    <td><span class="badge badge-success">{{ $present }}</span></td>
                    <td><span class="badge badge-danger">{{ $absent }}</span></td>
                    <td><span class="badge badge-primary">{{ $leave }}</span></td>
                    <td>{{ $lateDays ?: '—' }}</td>
                    <td><strong>{{ $totalHrs }}</strong></td>
                    <td>{{ $otMin > 0 ? $otHrs : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No data for this period.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

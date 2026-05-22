@extends('biometric::layouts.app')
@section('title','Daily Report')
@section('content')
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="form-inline" method="GET">
            <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ $date }}">
            <button class="btn btn-sm btn-outline-secondary mr-2">View</button>
            <a href="{{ route('biometric.reports.export',['type'=>'daily','format'=>'csv','date'=>$date]) }}"
               class="btn btn-sm btn-outline-success"><i class="fas fa-download mr-1"></i>CSV</a>
            <a href="{{ route('biometric.reports.export',['type'=>'daily','format'=>'json','date'=>$date]) }}"
               class="btn btn-sm btn-outline-info ml-1"><i class="fas fa-code mr-1"></i>JSON</a>
        </form>
    </div>
</div>

{{-- Summary cards --}}
<div class="row mb-3">
    @foreach([
        ['label'=>'Present','value'=>$summary['present'],'color'=>'success'],
        ['label'=>'Absent','value'=>$summary['absent'],'color'=>'danger'],
        ['label'=>'Late','value'=>$summary['late'],'color'=>'warning'],
        ['label'=>'On Leave','value'=>$summary['on_leave'],'color'=>'primary'],
        ['label'=>'Half Day','value'=>$summary['half_day'],'color'=>'info'],
    ] as $s)
    <div class="col mb-2">
        <div class="card text-center border-{{ $s['color'] }} shadow-sm">
            <div class="card-body py-2">
                <div class="h4 mb-0 text-{{ $s['color'] }}">{{ $s['value'] }}</div>
                <small class="text-muted">{{ $s['label'] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white font-weight-bold">
        Daily Report — {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr>
                    <th>Code</th><th>Name</th><th>Department</th><th>Shift</th>
                    <th>Check In</th><th>Check Out</th><th>Hours</th>
                    <th>Late</th><th>OT</th><th>Status</th>
                </tr></thead>
                <tbody>
                @forelse($rows as $r)
                @php $badge = ['present'=>'success','absent'=>'danger','on_leave'=>'primary','half_day'=>'warning'][$r->status] ?? 'secondary'; @endphp
                <tr>
                    <td>{{ $r->employee?->employee_code }}</td>
                    <td>{{ $r->employee?->name }}</td>
                    <td><small>{{ $r->employee?->department }}</small></td>
                    <td><small>{{ $r->shift?->name }}</small></td>
                    <td>{{ $r->check_in?->format('H:i') ?? '—' }}</td>
                    <td>{{ $r->check_out?->format('H:i') ?? '—' }}</td>
                    <td>{{ $r->workingHours() }}</td>
                    <td>{{ $r->late_minutes > 0 ? $r->late_minutes.'m' : '—' }}</td>
                    <td>{{ $r->overtime_minutes > 0 ? $r->overtime_minutes.'m' : '—' }}</td>
                    <td><span class="badge badge-{{ $badge }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No records for {{ $date }}.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

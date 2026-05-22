@extends('biometric::layouts.app')
@section('title','Shifts')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 font-weight-bold">Shift Definitions</h6>
    <a href="{{ route('biometric.shifts.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i>Add Shift</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="thead-light"><tr>
                <th>Name</th><th>Start</th><th>End</th><th>Grace Late</th>
                <th>Grace Early Out</th><th>OT After</th><th>Days</th><th>Type</th><th>Employees</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($shifts as $s)
            <tr>
                <td><strong>{{ $s->name }}</strong></td>
                <td>{{ $s->start_time }}</td>
                <td>{{ $s->end_time }}</td>
                <td>{{ $s->grace_late }} min</td>
                <td>{{ $s->grace_early_out }} min</td>
                <td>{{ $s->overtime_after }} min</td>
                <td><small>{{ $s->working_days }}</small></td>
                <td>
                    @if($s->is_flexible) <span class="badge badge-info">Flexible</span>
                    @elseif($s->is_overnight) <span class="badge badge-dark">Overnight</span>
                    @else <span class="badge badge-secondary">Standard</span>
                    @endif
                </td>
                <td><span class="badge badge-light">{{ $s->employees_count }}</span></td>
                <td class="text-right">
                    <a href="{{ route('biometric.shifts.edit',$s->id) }}" class="btn btn-xs btn-outline-secondary">Edit</a>
                    <form method="POST" action="{{ route('biometric.shifts.destroy',$s->id) }}" class="d-inline"
                          onsubmit="return confirm('Delete this shift?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">No shifts defined yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

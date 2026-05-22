@extends('biometric::layouts.app')
@section('title','Employees')
@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <form class="form-inline" method="GET">
                    <input name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Search name or code...">
                    <select name="department" class="form-control form-control-sm mr-2">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)<option value="{{ $d }}" {{ request('department')==$d?'selected':'' }}>{{ $d }}</option>@endforeach
                    </select>
                    <select name="status" class="form-control form-control-sm mr-2">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary mr-2">Filter</button>
                    <a href="{{ route('biometric.employees.index') }}" class="btn btn-sm btn-link">Clear</a>
                </form>
            </div>
            <div class="col-auto">
                <a href="{{ route('biometric.employees.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i>Add Employee</a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr>
                    <th>Code</th><th>Name</th><th>Department</th><th>Designation</th>
                    <th>ZK ID</th><th>Card</th><th>Status</th><th>Sync</th><th></th>
                </tr></thead>
                <tbody>
                @forelse($employees as $e)
                <tr>
                    <td><strong>{{ $e->employee_code }}</strong></td>
                    <td>{{ $e->name }}<br><small class="text-muted">{{ $e->email }}</small></td>
                    <td>{{ $e->department }}</td>
                    <td>{{ $e->designation }}</td>
                    <td><span class="badge badge-secondary">{{ $e->zk_user_id ?? '—' }}</span></td>
                    <td><small>{{ $e->card_number ?? '—' }}</small></td>
                    <td><span class="badge badge-{{ $e->status=='active'?'success':'secondary' }}">{{ $e->status }}</span></td>
                    <td><span class="badge badge-{{ $e->sync_status=='synced'?'info':'warning' }}">{{ $e->sync_status }}</span></td>
                    <td class="text-right">
                        <a href="{{ route('biometric.employees.show',$e->id) }}" class="btn btn-xs btn-outline-info">View</a>
                        <a href="{{ route('biometric.employees.edit',$e->id) }}" class="btn btn-xs btn-outline-secondary">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No employees found. <a href="{{ route('biometric.employees.create') }}">Add one</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($employees->hasPages())
    <div class="card-footer bg-white">{{ $employees->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

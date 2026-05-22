@extends('biometric::layouts.app')
@section('title','Leave Types')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold">Configured Leave Types</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr>
                        <th>Name</th><th>Days/Year</th><th>Paid</th><th>Carry Forward</th>
                    </tr></thead>
                    <tbody>
                    @foreach($types as $t)
                    <tr>
                        <td><strong>{{ $t->name }}</strong></td>
                        <td>{{ $t->days_allowed ?: 'Unlimited' }}</td>
                        <td>{{ $t->is_paid ? '✓' : '—' }}</td>
                        <td>{{ $t->carry_forward ? '✓' : '—' }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold">Add Leave Type</div>
            <div class="card-body">
                <form method="POST" action="{{ route('biometric.leave.types.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group">
                        <label>Days Allowed / Year <small class="text-muted">(0=unlimited)</small></label>
                        <input type="number" name="days_allowed" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_paid" value="1" checked>
                        <label class="form-check-label">Paid Leave</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="carry_forward" value="1">
                        <label class="form-check-label">Carry Forward</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary btn-block">Add Type</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

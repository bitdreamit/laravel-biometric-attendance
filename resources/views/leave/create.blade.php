@extends('biometric::layouts.app')
@section('title','Apply Leave')
@section('content')
<div class="card shadow-sm" style="max-width:600px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-calendar-minus mr-2"></i>Apply Leave</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('biometric.leave.store') }}">
            @csrf
            <div class="form-group">
                <label>Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">— Select Employee —</option>
                    @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ old('employee_id')==$e->id?'selected':'' }}>
                        {{ $e->employee_code }} — {{ $e->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Leave Type <span class="text-danger">*</span></label>
                <select name="leave_type_id" class="form-control" required>
                    <option value="">— Select Type —</option>
                    @foreach($leaveTypes as $lt)
                    <option value="{{ $lt->id }}" {{ old('leave_type_id')==$lt->id?'selected':'' }}>
                        {{ $lt->name }} ({{ $lt->days_allowed }} days/year, {{ $lt->is_paid?'Paid':'Unpaid' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>From Date <span class="text-danger">*</span></label>
                    <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>To Date <span class="text-danger">*</span></label>
                    <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Apply Leave</button>
            <a href="{{ route('biometric.leave.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

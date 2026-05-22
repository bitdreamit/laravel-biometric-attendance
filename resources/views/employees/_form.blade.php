@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
@endif
<div class="row">
    <div class="col-md-6 form-group">
        <label>Employee Code <span class="text-danger">*</span></label>
        <input name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code',$employee?->employee_code) }}" required>
        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Full Name <span class="text-danger">*</span></label>
        <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$employee?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email',$employee?->email) }}">
    </div>
    <div class="col-md-6 form-group">
        <label>Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone',$employee?->phone) }}">
    </div>
    <div class="col-md-6 form-group">
        <label>Department</label>
        <input name="department" class="form-control" list="dept-list" value="{{ old('department',$employee?->department) }}">
        <datalist id="dept-list">
            @foreach(app(config('biometric.models.employee'))::distinct()->pluck('department')->filter() as $d)
            <option value="{{ $d }}">@endforeach
        </datalist>
    </div>
    <div class="col-md-6 form-group">
        <label>Designation</label>
        <input name="designation" class="form-control" value="{{ old('designation',$employee?->designation) }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Join Date</label>
        <input type="date" name="join_date" class="form-control" value="{{ old('join_date',$employee?->join_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 form-group">
        <label>ZK User ID <small class="text-muted">(1–65535)</small></label>
        <input type="number" name="zk_user_id" class="form-control @error('zk_user_id') is-invalid @enderror" min="1" max="65535" value="{{ old('zk_user_id',$employee?->zk_user_id) }}">
        <small class="form-text text-muted">Integer ID enrolled on ZKTeco device</small>
        @error('zk_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Card Number <small class="text-muted">(RFID)</small></label>
        <input name="card_number" class="form-control" value="{{ old('card_number',$employee?->card_number) }}">
    </div>
    <div class="col-md-6 form-group">
        <label>Status <span class="text-danger">*</span></label>
        <select name="status" class="form-control" required>
            <option value="active"   {{ old('status',$employee?->status)=='active'   ?'selected':'' }}>Active</option>
            <option value="inactive" {{ old('status',$employee?->status)=='inactive' ?'selected':'' }}>Inactive</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Assign Shift</label>
        <select name="shift_id" class="form-control">
            <option value="">— No shift —</option>
            @foreach($shifts as $s)
            <option value="{{ $s->id }}" {{ old('shift_id')==$s->id?'selected':'' }}>{{ $s->name }} ({{ $s->start_time }}–{{ $s->end_time }})</option>
            @endforeach
        </select>
    </div>
</div>

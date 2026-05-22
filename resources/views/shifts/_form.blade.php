@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
@endif
@php $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; @endphp
@php $selectedDays = old('working_days', isset($shift) ? $shift->workingDaysArray() : ['Mon','Tue','Wed','Thu','Fri']); @endphp
<div class="row">
    <div class="col-md-6 form-group">
        <label>Shift Name <span class="text-danger">*</span></label>
        <input name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $shift?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $shift?->start_time) }}" required>
    </div>
    <div class="col-md-3 form-group">
        <label>End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $shift?->end_time) }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Grace Late (minutes)</label>
        <input type="number" name="grace_late" class="form-control" min="0" max="120"
               value="{{ old('grace_late', $shift?->grace_late ?? 15) }}">
        <small class="text-muted">Minutes allowed after start before marked late</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Grace Early Out (minutes)</label>
        <input type="number" name="grace_early_out" class="form-control" min="0" max="120"
               value="{{ old('grace_early_out', $shift?->grace_early_out ?? 10) }}">
        <small class="text-muted">Minutes before end before marked early leave</small>
    </div>
    <div class="col-md-4 form-group">
        <label>Overtime After (minutes)</label>
        <input type="number" name="overtime_after" class="form-control" min="0" max="240"
               value="{{ old('overtime_after', $shift?->overtime_after ?? 30) }}">
        <small class="text-muted">Minutes past end before overtime counted</small>
    </div>
    <div class="col-12 form-group">
        <label>Working Days <span class="text-danger">*</span></label>
        <div>
            @foreach($days as $d)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="working_days[]"
                       id="day_{{ $d }}" value="{{ $d }}"
                       {{ in_array($d, $selectedDays) ? 'checked' : '' }}>
                <label class="form-check-label" for="day_{{ $d }}">{{ $d }}</label>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-6 form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_overnight" id="is_overnight" value="1"
                   {{ old('is_overnight', $shift?->is_overnight) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_overnight">
                <strong>Overnight shift</strong> <small class="text-muted">(crosses midnight, e.g. 22:00–06:00)</small>
            </label>
        </div>
    </div>
    <div class="col-md-6 form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_flexible" id="is_flexible" value="1"
                   {{ old('is_flexible', $shift?->is_flexible) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_flexible">
                <strong>Flexible shift</strong> <small class="text-muted">(no late/early-out rules applied)</small>
            </label>
        </div>
    </div>
</div>

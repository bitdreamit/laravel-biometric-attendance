@extends('biometric::layouts.app')
@section('title','Edit Device')
@section('content')
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-tablet-alt mr-2"></i>Edit Device</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('biometric.devices.update',$device->id) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Serial Number</label>
                <input class="form-control" value="{{ $device->serial_number }}" disabled>
            </div>
            <div class="form-group">
                <label>Friendly Name <span class="text-danger">*</span></label>
                <input name="name" class="form-control" value="{{ old('name',$device->name) }}" required>
            </div>
            <div class="row">
                <div class="col-8 form-group">
                    <label>IP Address</label>
                    <input name="ip" class="form-control" value="{{ old('ip',$device->ip) }}">
                </div>
                <div class="col-4 form-group">
                    <label>Port</label>
                    <input type="number" name="port" class="form-control" value="{{ old('port',$device->port) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input name="location" class="form-control" value="{{ old('location',$device->location) }}">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                       {{ $device->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('biometric.devices.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

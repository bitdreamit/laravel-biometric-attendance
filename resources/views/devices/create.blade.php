@extends('biometric::layouts.app')
@section('title','Add Device')
@section('content')
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-tablet-alt mr-2"></i>Add ZKTeco Device</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('biometric.devices.store') }}">
            @csrf
            <div class="form-group">
                <label>Serial Number <span class="text-danger">*</span></label>
                <input name="serial_number" class="form-control" value="{{ old('serial_number') }}" required>
                <small class="text-muted">Found on device label or Menu → System Info</small>
            </div>
            <div class="form-group">
                <label>Friendly Name <span class="text-danger">*</span></label>
                <input name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Main Entrance" required>
            </div>
            <div class="row">
                <div class="col-8 form-group">
                    <label>IP Address</label>
                    <input name="ip" class="form-control" value="{{ old('ip','192.168.1.') }}" placeholder="192.168.1.201">
                </div>
                <div class="col-4 form-group">
                    <label>Port</label>
                    <input type="number" name="port" class="form-control" value="{{ old('port',4370) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input name="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Ground Floor, Block A">
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Add Device</button>
            <a href="{{ route('biometric.devices.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

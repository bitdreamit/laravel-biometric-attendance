@extends('biometric::layouts.app')
@section('title','Edit Shift')
@section('content')
<div class="card shadow-sm" style="max-width:700px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-clock mr-2"></i>Edit Shift</div>
    <div class="card-body">
        <form method="POST" action="{{ route('biometric.shifts.update',$shift->id) }}">
            @csrf @method('PUT')
            @include('biometric::shifts._form', ['shift'=>$shift])
            <hr>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('biometric.shifts.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

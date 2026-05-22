@extends('biometric::layouts.app')
@section('title','Create Shift')
@section('content')
<div class="card shadow-sm" style="max-width:700px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-clock mr-2"></i>Create Shift</div>
    <div class="card-body">
        <form method="POST" action="{{ route('biometric.shifts.store') }}">
            @csrf
            @include('biometric::shifts._form', ['shift'=>null])
            <hr>
            <button type="submit" class="btn btn-primary">Save Shift</button>
            <a href="{{ route('biometric.shifts.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

@extends('biometric::layouts.app')
@section('title','Add Employee')
@section('content')
<div class="card shadow-sm" style="max-width:700px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Register Employee</div>
    <div class="card-body">
        <form method="POST" action="{{ route('biometric.employees.store') }}">
            @csrf
            @include('biometric::employees._form', ['employee'=>null])
            <hr>
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="{{ route('biometric.employees.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

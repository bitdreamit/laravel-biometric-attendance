@extends('biometric::layouts.app')
@section('title','Edit Employee')
@section('content')
<div class="card shadow-sm" style="max-width:700px">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-user-edit mr-2"></i>Edit Employee</div>
    <div class="card-body">
        <form method="POST" action="{{ route('biometric.employees.update',$employee->id) }}">
            @csrf @method('PUT')
            @include('biometric::employees._form', ['employee'=>$employee])
            <hr>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('biometric.employees.show',$employee->id) }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection

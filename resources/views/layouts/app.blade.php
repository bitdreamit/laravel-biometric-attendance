<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Biometric Attendance') — {{ config('app.name') }}</title>
    <!-- Bootstrap 4.6 CDN — override with your own by publishing views -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body{background:#f4f6f9}
        .sidebar{min-height:100vh;background:#343a40;padding-top:1rem}
        .sidebar a{color:#adb5bd;display:block;padding:.5rem 1rem;border-radius:4px}
        .sidebar a:hover,.sidebar a.active{background:#495057;color:#fff;text-decoration:none}
        .sidebar .nav-section{color:#6c757d;font-size:.75rem;font-weight:600;text-transform:uppercase;padding:.5rem 1rem;margin-top:1rem}
        .main-content{padding:1.5rem}
        .stat-card{border-radius:8px;border:none}
        .table th{font-weight:600;font-size:.85rem;color:#6c757d;border-top:none}
        .badge-present{background:#d4edda;color:#155724}
        .badge-absent{background:#f8d7da;color:#721c24}
        .badge-late{background:#fff3cd;color:#856404}
        .badge-leave{background:#cce5ff;color:#004085}
    </style>
    @yield('styles')
</head>
<body>

@php $layout = config('biometric.layout') @endphp
@if($layout)
    @extends($layout)
@else

<div class="container-fluid p-0">
    <div class="row no-gutters">

        {{-- Sidebar --}}
        <div class="col-auto sidebar d-none d-md-block" style="width:230px">
            <div class="px-3 py-2 mb-2">
                <span class="text-white font-weight-bold"><i class="fas fa-fingerprint mr-2"></i>Biometric</span>
            </div>
            <nav>
                <a href="{{ route('biometric.dashboard') }}" class="{{ request()->routeIs('biometric.dashboard') ? 'active':'' }}">
                    <i class="fas fa-tachometer-alt fa-fw mr-2"></i>Dashboard
                </a>
                <div class="nav-section">Devices & Staff</div>
                <a href="{{ route('biometric.devices.index') }}" class="{{ request()->routeIs('biometric.devices.*') ? 'active':'' }}">
                    <i class="fas fa-tablet-alt fa-fw mr-2"></i>Devices
                </a>
                <a href="{{ route('biometric.employees.index') }}" class="{{ request()->routeIs('biometric.employees.*') ? 'active':'' }}">
                    <i class="fas fa-users fa-fw mr-2"></i>Employees
                </a>
                <a href="{{ route('biometric.shifts.index') }}" class="{{ request()->routeIs('biometric.shifts.*') ? 'active':'' }}">
                    <i class="fas fa-clock fa-fw mr-2"></i>Shifts
                </a>
                <div class="nav-section">Attendance</div>
                <a href="{{ route('biometric.attendance.index') }}" class="{{ request()->routeIs('biometric.attendance.index') ? 'active':'' }}">
                    <i class="fas fa-calendar-check fa-fw mr-2"></i>Daily Log
                </a>
                <a href="{{ route('biometric.attendance.punches') }}" class="{{ request()->routeIs('biometric.attendance.punches') ? 'active':'' }}">
                    <i class="fas fa-list fa-fw mr-2"></i>Raw Punches
                </a>
                <div class="nav-section">Leave</div>
                <a href="{{ route('biometric.leave.index') }}" class="{{ request()->routeIs('biometric.leave.*') ? 'active':'' }}">
                    <i class="fas fa-calendar-minus fa-fw mr-2"></i>Leave Requests
                </a>
                <div class="nav-section">Reports</div>
                <a href="{{ route('biometric.reports.daily') }}" class="{{ request()->routeIs('biometric.reports.daily') ? 'active':'' }}">
                    <i class="fas fa-chart-bar fa-fw mr-2"></i>Daily Report
                </a>
                <a href="{{ route('biometric.reports.monthly') }}" class="{{ request()->routeIs('biometric.reports.monthly') ? 'active':'' }}">
                    <i class="fas fa-chart-line fa-fw mr-2"></i>Monthly Report
                </a>
            </nav>
        </div>

        {{-- Main --}}
        <div class="col main-content">
            {{-- Top navbar --}}
            <nav class="navbar navbar-light bg-white shadow-sm mb-4 rounded">
                <span class="navbar-text font-weight-bold">@yield('title','Dashboard')</span>
                <div class="ml-auto">
                    <span class="text-muted mr-3 small">{{ auth()->user()?->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">Logout</button>
                    </form>
                </div>
            </nav>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>

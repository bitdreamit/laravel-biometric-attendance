<?php
return [
    /*
    |--------------------------------------------------------------------------
    | UI / Theme
    |--------------------------------------------------------------------------
    | null      → Bootstrap 4.6 default (zero dependencies)
    | 'layouts.app' → your own Blade layout (must yield 'content','styles','scripts')
    */
    'layout' => null,
    'theme'  => 'bootstrap4',  // bootstrap4 | tailwind | custom

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */
    'api_prefix'     => 'api/biometric',
    'api_middleware' => ['api', 'auth:sanctum'],
    'web_prefix'     => 'biometric',
    'web_middleware' => ['web', 'auth'],
    'web_enabled'    => true,

    /*
    |--------------------------------------------------------------------------
    | Multi-tenancy
    |--------------------------------------------------------------------------
    | null → single-tenant (no tenant_id used anywhere)
    */
    'tenant_model'  => null,
    'tenant_column' => 'tenant_id',
    'tenant_scope'  => false,

    /*
    |--------------------------------------------------------------------------
    | Model overrides
    |--------------------------------------------------------------------------
    */
    'models' => [
        'device'         => \Bitdreamit\BiometricAttendance\Models\Device::class,
        'employee'       => \Bitdreamit\BiometricAttendance\Models\Employee::class,
        'shift'          => \Bitdreamit\BiometricAttendance\Models\Shift::class,
        'attendance'     => \Bitdreamit\BiometricAttendance\Models\Attendance::class,
        'attendance_log' => \Bitdreamit\BiometricAttendance\Models\AttendanceLog::class,
        'leave_type'     => \Bitdreamit\BiometricAttendance\Models\LeaveType::class,
        'leave_request'  => \Bitdreamit\BiometricAttendance\Models\LeaveRequest::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance processing
    |--------------------------------------------------------------------------
    */
    'attendance' => [
        'duplicate_window_seconds' => 60,
        'auto_process'             => true,
        'timezone'                 => env('APP_TIMEZONE', 'Asia/Dhaka'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Agent settings
    |--------------------------------------------------------------------------
    */
    'agent' => [
        'token_name'    => 'biometric-agent',
        'poll_interval' => 300,       // seconds (for display purposes)
        'offline_threshold_minutes' => 15,  // minutes before device marked offline
    ],

    'per_page' => 25,
];

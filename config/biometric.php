<?php
return [
    /*
    |--------------------------------------------------------------------------
    | UI Layout — customisable theme
    |--------------------------------------------------------------------------
    | null      → package default (Bootstrap 4.6, zero dependencies)
    | 'layouts.app' → your own Blade layout (must yield 'content','styles','scripts')
    |
    | Tailwind users: set theme=>'tailwind', layout=>'layouts.app'
    | Filament users: set web_enabled=>false (use your own Filament resource)
    */
    'layout'  => null,
    'theme'   => 'bootstrap4',   // bootstrap4 | tailwind | custom

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
    | tenant_model  = null  → single tenant (no tenant_id used)
    | tenant_model  = App\Models\Company::class → multi-tenant
    | tenant_column = column on employees/attendance tables (default: tenant_id)
    | tenant_scope  = true  → auto-scope all queries (requires HasBiometricTenant trait)
    */
    'tenant_model'  => null,
    'tenant_column' => 'tenant_id',
    'tenant_scope'  => false,

    /*
    |--------------------------------------------------------------------------
    | Model overrides — swap any model with your own
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
    | Attendance processing rules
    |--------------------------------------------------------------------------
    */
    'attendance' => [
        'duplicate_window_seconds' => 60,
        'auto_process'             => true,
        'timezone'                 => env('APP_TIMEZONE', 'Asia/Dhaka'),
    ],

    'per_page' => 25,
];

<?php
use Illuminate\Support\Facades\Route;
use Bitdreamit\BiometricAttendance\Http\Controllers\Web\{
    DashboardController, EmployeeController, ShiftController,
    AttendanceController, DeviceController, LeaveController, ReportController
};

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Devices
Route::resource('devices', DeviceController::class)->except(['show']);

// Employees (+ custom sync action)
Route::post('employees/{id}/sync', [EmployeeController::class, 'sync'])->name('employees.sync');
Route::resource('employees', EmployeeController::class);

// Shifts
Route::resource('shifts', ShiftController::class)->except(['show']);

// Attendance
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/',        [AttendanceController::class, 'index'])  ->name('index');
    Route::get('punches',  [AttendanceController::class, 'punches'])->name('punches');
    Route::post('process', [AttendanceController::class, 'process'])->name('process');
});

// Leave
Route::prefix('leave')->name('leave.')->group(function () {
    Route::get('/',              [LeaveController::class, 'index'])     ->name('index');
    Route::get('create',         [LeaveController::class, 'create'])    ->name('create');
    Route::post('/',             [LeaveController::class, 'store'])     ->name('store');
    Route::post('{id}/approve',  [LeaveController::class, 'approve'])   ->name('approve');
    Route::post('{id}/reject',   [LeaveController::class, 'reject'])    ->name('reject');
    Route::get('types',          [LeaveController::class, 'types'])     ->name('types');
    Route::post('types',         [LeaveController::class, 'storeType']) ->name('types.store');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('daily',   [ReportController::class, 'daily'])  ->name('daily');
    Route::get('monthly', [ReportController::class, 'monthly'])->name('monthly');
    Route::get('export',  [ReportController::class, 'export']) ->name('export');
});

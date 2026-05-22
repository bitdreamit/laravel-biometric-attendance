<?php
use Illuminate\Support\Facades\Route;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentEmployeeController;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentAttendanceController;

/**
 * These routes are consumed by the offline Python agent.
 * Prefix: /api/biometric (configurable via config('biometric.api_prefix'))
 * Auth:   Bearer token (Laravel Sanctum by default)
 */
Route::get ('employees',  [AgentEmployeeController::class,  'index']);
Route::post('attendance', [AgentAttendanceController::class, 'store']);

<?php
use Illuminate\Support\Facades\Route;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentEmployeeController;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentAttendanceController;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentHeartbeatController;
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentCommandController;

/**
 * API routes consumed by the offline Python agent.
 * Prefix  : /api/biometric   (config: biometric.api_prefix)
 * Auth    : auth:sanctum + AgentTokenMiddleware (agent-only token + rate limit)
 *
 * ENDPOINTS
 * ─────────────────────────────────────────────────────────────────────
 * GET  employees          Agent pulls employee list every N minutes
 * POST attendance         Agent pushes raw punch batch
 * POST heartbeat          Agent reports health + device status (optional)
 * GET  agent-commands     Agent polls for pending commands (optional)
 */

Route::middleware(\Bitdreamit\BiometricAttendance\Http\Middleware\AgentTokenMiddleware::class)
    ->group(function () {
        Route::get ('employees',      [AgentEmployeeController::class,  'index']);
        Route::post('attendance',     [AgentAttendanceController::class, 'store']);
        Route::post('heartbeat',      [AgentHeartbeatController::class, 'store']);
        Route::get ('agent-commands', [AgentCommandController::class,   'index']);
    });

<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * POST /api/biometric/heartbeat
 * Agent posts this every sync cycle so Laravel knows it is alive.
 */
class AgentHeartbeatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data     = $request->validate([
            'agent_version' => 'nullable|string',
            'tenant_id'     => 'nullable|string',
            'devices'       => 'nullable|array',
            'queue'         => 'nullable|array',
        ]);

        $tenantId = $request->header('X-Tenant-ID') ?? $data['tenant_id'] ?? null;
        $devModel = app(config('biometric.models.device'));

        // Update last_seen_at for each reported device
        foreach ($data['devices'] ?? [] as $dev) {
            $sn = $dev['serial_number'] ?? null;
            if ($sn) {
                $devModel::where('serial_number', $sn)
                    ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                    ->update([
                        'last_seen_at' => now(),
                        'ip'           => $dev['ip'] ?? null,
                    ]);
            }
        }

        // Store agent heartbeat metadata in cache for dashboard
        $cacheKey = 'biometric_agent_heartbeat' . ($tenantId ? ":$tenantId" : '');
        if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, [
                'last_seen'     => now()->toISOString(),
                'agent_version' => $data['agent_version'] ?? 'unknown',
                'tenant_id'     => $tenantId,
                'devices'       => $data['devices'] ?? [],
                'queue'         => $data['queue'] ?? [],
            ], now()->addMinutes(15));
        }

        return response()->json(['status' => 'ok', 'server_time' => now()->toISOString()]);
    }
}

<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * GET /api/biometric/agent-commands
 * Agent polls this every sync cycle.
 * Returns pending commands: enroll_user, clear_device_log, sync_now.
 *
 * Commands are stored in the biometric_agent_commands table
 * (or a simple cache queue if no table exists yet).
 */
class AgentCommandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID');
        $commands = [];

        // Pull commands from cache queue (simple approach, no extra table needed)
        $key  = 'biometric_agent_commands' . ($tenantId ? ":$tenantId" : '');
        if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
            $queued   = \Illuminate\Support\Facades\Cache::pull($key, []);
            $commands = is_array($queued) ? $queued : [];
        }

        return response()->json(['commands' => $commands]);
    }

    /**
     * Utility: queue a command for the next agent poll.
     * Call from your own code: AgentCommandController::queue('sync_now', [], $tenantId)
     */
    public static function queue(string $type, array $data = [], ?string $tenantId = null): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Cache::class)) return;

        $key      = 'biometric_agent_commands' . ($tenantId ? ":$tenantId" : '');
        $existing = \Illuminate\Support\Facades\Cache::get($key, []);
        $existing[] = ['type' => $type, 'data' => $data, 'queued_at' => now()->toISOString()];
        \Illuminate\Support\Facades\Cache::put($key, $existing, now()->addHours(24));
    }
}

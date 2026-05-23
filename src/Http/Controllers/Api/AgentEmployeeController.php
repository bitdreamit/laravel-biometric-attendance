<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * GET /api/biometric/employees
 * Called by the offline Python agent to sync employee list.
 * Returns active employees + deleted_ids for soft-deleted ones.
 */
class AgentEmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID');
        $model    = app(config('biometric.models.employee'));

        $employees = $model::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'active')
            ->get()
            ->map(fn($e) => $e->toAgentArray());

        // Mark all returned employees as synced
        $model::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('sync_status', 'pending')
            ->update(['sync_status' => 'synced', 'synced_at' => now()]);

        $deleted = $model::onlyTrashed()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->values();

        return response()->json([
            'data'        => $employees,
            'deleted_ids' => $deleted,
        ]);
    }
}

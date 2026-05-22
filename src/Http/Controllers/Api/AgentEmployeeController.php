<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * GET /api/biometric/employees
 * Called by the offline Python agent to sync employee list.
 */
class AgentEmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID') ?? config('biometric.tenant_column');
        $model    = app(config('biometric.models.employee'));

        $employees = $model::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('status', 'active')
            ->get()
            ->map(fn($e) => $e->toAgentArray());

        // Soft-deleted (removed from server, agent should remove from device)
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

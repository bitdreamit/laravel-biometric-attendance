<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;

/**
 * POST /api/biometric/attendance
 * Called by the offline Python agent to push raw punch records.
 */
class AgentAttendanceController extends Controller
{
    public function __construct(private AttendanceProcessorService $processor) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate(['punches' => 'required|array', 'punches.*.zk_user_id' => 'required', 'punches.*.punched_at' => 'required']);

        $tenantId  = $request->header('X-Tenant-ID');
        $model     = app(config('biometric.models.attendance'));
        $empModel  = app(config('biometric.models.employee'));

        $synced     = [];
        $duplicates = [];
        $errors     = [];

        foreach ($request->input('punches', []) as $punch) {
            $localId = $punch['id'] ?? null;

            try {
                // Resolve employee
                $employee = $empModel::query()
                    ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                    ->where(function ($q) use ($punch) {
                        $q->where('zk_user_id', $punch['zk_user_id'])
                          ->orWhere('employee_code', $punch['employee_code'] ?? '');
                    })
                    ->first();

                // Duplicate check: same user + device + timestamp (within window)
                $window   = config('biometric.attendance.duplicate_window_seconds', 60);
                $punchedAt = $punch['punched_at'];
                $exists = $model::query()
                    ->where('zk_user_id', $punch['zk_user_id'])
                    ->where('device_sn',  $punch['device_sn'] ?? '')
                    ->whereBetween('punched_at', [
                        \Carbon\Carbon::parse($punchedAt)->subSeconds($window),
                        \Carbon\Carbon::parse($punchedAt)->addSeconds($window),
                    ])
                    ->exists();

                if ($exists) {
                    $duplicates[] = $localId;
                    continue;
                }

                // Determine punch type (check_in / check_out)
                $punchType = $this->determinePunchType($employee, $punchedAt, $tenantId);

                DB::beginTransaction();
                $record = $model::create([
                    'tenant_id'    => $punch['tenant_id'] ?? $tenantId,
                    'zk_user_id'   => $punch['zk_user_id'],
                    'employee_id'  => $employee?->id,
                    'employee_code'=> $punch['employee_code'] ?? $employee?->employee_code,
                    'punched_at'   => $punchedAt,
                    'device_sn'    => $punch['device_sn'] ?? null,
                    'device_ip'    => $punch['device_ip'] ?? null,
                    'punch_type'   => $punchType,
                    'verify_type'  => $punch['verify_type'] ?? 'fingerprint',
                    'source'       => 'agent',
                    'sync_status'  => 'received',
                ]);

                // Auto-process into attendance_log
                if (config('biometric.attendance.auto_process') && $employee) {
                    $this->processor->processForEmployee($employee, $record->punched_at->toDateString());
                }

                DB::commit();
                $synced[] = ['id' => $localId, 'remote_id' => (string)$record->id];

            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = ['id' => $localId, 'error' => $e->getMessage()];
            }
        }

        return response()->json(compact('synced', 'duplicates', 'errors'), 207);
    }

    private function determinePunchType(?object $employee, string $punchedAt, ?string $tenantId): string
    {
        if (!$employee) return 'unknown';

        $model  = app(config('biometric.models.attendance'));
        $date   = \Carbon\Carbon::parse($punchedAt)->toDateString();

        $lastPunch = $model::where('employee_id', $employee->id)
            ->whereDate('punched_at', $date)
            ->orderBy('punched_at', 'desc')
            ->first();

        if (!$lastPunch) return 'check_in';
        return $lastPunch->punch_type === 'check_in' ? 'check_out' : 'check_in';
    }
}

<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $empModel = app(config('biometric.models.employee'));
        $attModel = app(config('biometric.models.attendance'));
        $logModel = app(config('biometric.models.attendance_log'));
        $devModel = app(config('biometric.models.device'));
        $today    = now()->toDateString();

        $stats = [
            'total_employees' => $empModel::active()->count(),
            'total_devices'   => $devModel::active()->count(),
            'present_today'   => $logModel::where('work_date', $today)->where('status', 'present')->count(),
            'absent_today'    => $logModel::where('work_date', $today)->where('status', 'absent')->count(),
            'late_today'      => $logModel::where('work_date', $today)->where('late_minutes', '>', 0)->count(),
            'punches_today'   => $attModel::whereDate('punched_at', $today)->count(),
        ];

        $recent_punches = $attModel::with('employee')
            ->whereDate('punched_at', $today)
            ->orderByDesc('punched_at')
            ->limit(10)
            ->get();

        // Devices with last_seen_at for status indicators
        $devices = $devModel::active()->orderBy('name')->get();

        // Agent heartbeat from cache
        $agent_status = null;
        if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
            $tenantId = config('biometric.tenant_scope') ? $request->header('X-Tenant-ID') : null;
            $key      = 'biometric_agent_heartbeat' . ($tenantId ? ":$tenantId" : '');
            $hb       = \Illuminate\Support\Facades\Cache::get($key);

            $agent_status = [
                'online'   => $hb !== null,
                'last_seen'=> $hb ? \Carbon\Carbon::parse($hb['last_seen'])->diffForHumans() : null,
                'version'  => $hb['agent_version'] ?? 'unknown',
                'queue'    => $hb['queue'] ?? [],
            ];
        }

        return view('biometric::dashboard.index',
            compact('stats', 'recent_punches', 'devices', 'agent_status'));
    }
}

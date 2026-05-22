<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $empModel   = app(config('biometric.models.employee'));
        $attModel   = app(config('biometric.models.attendance'));
        $logModel   = app(config('biometric.models.attendance_log'));
        $devModel   = app(config('biometric.models.device'));
        $today      = now()->toDateString();

        $stats = [
            'total_employees' => $empModel::active()->count(),
            'total_devices'   => $devModel::active()->count(),
            'present_today'   => $logModel::whereDate('work_date',$today)->where('status','present')->count(),
            'absent_today'    => $logModel::whereDate('work_date',$today)->where('status','absent')->count(),
            'late_today'      => $logModel::whereDate('work_date',$today)->where('late_minutes','>',0)->count(),
            'punches_today'   => $attModel::whereDate('punched_at',$today)->count(),
        ];

        $recent_punches = $attModel::with('employee')
            ->whereDate('punched_at',$today)
            ->orderByDesc('punched_at')
            ->limit(10)
            ->get();

        return view('biometric::dashboard.index', compact('stats','recent_punches'));
    }
}

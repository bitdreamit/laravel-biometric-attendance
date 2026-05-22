<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date     = $request->date ?? now()->toDateString();
        $logModel = app(config('biometric.models.attendance_log'));

        $rows = $logModel::with(['employee','shift'])
            ->where('work_date',$date)
            ->get();

        $summary = [
            'present'  => $rows->where('status','present')->count(),
            'absent'   => $rows->where('status','absent')->count(),
            'late'     => $rows->where('late_minutes','>',0)->count(),
            'on_leave' => $rows->where('status','on_leave')->count(),
            'half_day' => $rows->where('status','half_day')->count(),
        ];

        return view('biometric::reports.daily', compact('rows','date','summary'));
    }

    public function monthly(Request $request)
    {
        $year    = (int)($request->year  ?? now()->year);
        $month   = (int)($request->month ?? now()->month);
        $from    = \Carbon\Carbon::create($year,$month,1)->startOfMonth()->toDateString();
        $to      = \Carbon\Carbon::create($year,$month,1)->endOfMonth()->toDateString();
        $logModel= app(config('biometric.models.attendance_log'));
        $empModel= app(config('biometric.models.employee'));

        $employees = $empModel::active()->with(['attendanceLogs'=>fn($q)=>$q->whereBetween('work_date',[$from,$to])])->get();

        return view('biometric::reports.monthly', compact('employees','year','month','from','to'));
    }

    public function export(Request $request)
    {
        $request->validate(['type'=>'required|in:daily,monthly','format'=>'required|in:csv,json']);

        $date  = $request->date ?? now()->toDateString();
        $model = app(config('biometric.models.attendance_log'));
        $rows  = $model::with('employee')->where('work_date',$date)->get();

        if ($request->format === 'json') {
            return response()->json($rows)->header('Content-Disposition','attachment; filename="attendance-'.$date.'.json"');
        }

        // CSV
        $csv  = "Employee Code,Name,Department,Date,Check In,Check Out,Working Hours,Late (min),Overtime (min),Status\n";
        foreach ($rows as $r) {
            $csv .= implode(',', [
                $r->employee?->employee_code ?? '',
                '"'.($r->employee?->name ?? '').'"',
                '"'.($r->employee?->department ?? '').'"',
                $r->work_date->format('Y-m-d'),
                $r->check_in?->format('H:i') ?? '',
                $r->check_out?->format('H:i') ?? '',
                $r->workingHours(),
                $r->late_minutes,
                $r->overtime_minutes,
                $r->status,
            ])."\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-'.$date.'.csv"',
        ]);
    }
}

<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceProcessorService $processor) {}

    public function index(Request $request)
    {
        $logModel = app(config('biometric.models.attendance_log'));
        $date     = $request->date ?? now()->toDateString();

        $logs = $logModel::with(['employee','shift'])
            ->where('work_date',$date)
            ->when($request->department, fn($q,$d)=>$q->whereHas('employee',fn($q)=>$q->where('department',$d)))
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))
            ->orderBy(fn($q)=>$q->select('name')->from('biometric_employees')->whereColumn('id','employee_id'))
            ->paginate(config('biometric.per_page',25));

        $statuses    = ['present','absent','half_day','on_leave','holiday','weekend'];
        $departments = app(config('biometric.models.employee'))::distinct()->pluck('department')->filter()->sort();

        return view('biometric::attendance.index', compact('logs','date','statuses','departments'));
    }

    public function punches(Request $request)
    {
        $model = app(config('biometric.models.attendance'));
        $punches = $model::with('employee')
            ->when($request->date, fn($q,$d)=>$q->whereDate('punched_at',$d))
            ->when($request->employee_id, fn($q,$id)=>$q->where('employee_id',$id))
            ->orderByDesc('punched_at')
            ->paginate(config('biometric.per_page',25));

        return view('biometric::attendance.punches', compact('punches'));
    }

    public function process(Request $request)
    {
        $request->validate(['date'=>'required|date']);
        $count = $this->processor->processDate($request->date);
        return back()->with('success',"Processed $count employees for {$request->date}.");
    }
}

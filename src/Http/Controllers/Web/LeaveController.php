<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LeaveController extends Controller
{
    private function model()     { return app(config('biometric.models.leave_request')); }
    private function typeModel() { return app(config('biometric.models.leave_type')); }

    public function index(Request $request)
    {
        $requests = $this->model()::with(['employee','leaveType'])
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))
            ->when($request->employee_id, fn($q,$id)=>$q->where('employee_id',$id))
            ->orderByDesc('created_at')
            ->paginate(config('biometric.per_page',25));

        return view('biometric::leave.index', compact('requests'));
    }

    public function create()
    {
        $employees  = app(config('biometric.models.employee'))::active()->orderBy('name')->get();
        $leaveTypes = $this->typeModel()::orderBy('name')->get();
        return view('biometric::leave.create', compact('employees','leaveTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'   => 'required|exists:biometric_employees,id',
            'leave_type_id' => 'required|exists:biometric_leave_types,id',
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'reason'        => 'nullable|string|max:500',
        ]);

        $from = \Carbon\Carbon::parse($data['from_date']);
        $to   = \Carbon\Carbon::parse($data['to_date']);
        $data['days']      = $from->diffInWeekdays($to) + 1;
        $data['tenant_id'] = config('biometric.tenant_scope') ? request()->header('X-Tenant-ID') : null;

        $this->model()::create($data);
        return redirect()->route('biometric.leave.index')->with('success','Leave applied.');
    }

    public function approve($id)
    {
        $this->model()::findOrFail($id)->approve();
        return back()->with('success','Leave approved.');
    }

    public function reject($id)
    {
        $this->model()::findOrFail($id)->reject();
        return back()->with('success','Leave rejected.');
    }

    // Leave types CRUD
    public function types()         { return view('biometric::leave.types', ['types' => $this->typeModel()::all()]); }
    public function storeType(Request $request)
    {
        $request->validate(['name'=>'required|string','days_allowed'=>'required|integer|min:0','is_paid'=>'nullable|boolean','carry_forward'=>'nullable|boolean']);
        $this->typeModel()::create($request->only('name','days_allowed','is_paid','carry_forward'));
        return back()->with('success','Leave type added.');
    }
}

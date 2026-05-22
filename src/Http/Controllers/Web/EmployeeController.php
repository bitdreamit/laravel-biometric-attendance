<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    private function model() { return app(config('biometric.models.employee')); }

    public function index(Request $request)
    {
        $employees = $this->model()::with('shifts')
            ->when($request->search, fn($q,$s)=>$q->where('name','like',"%$s%")->orWhere('employee_code','like',"%$s%"))
            ->when($request->department, fn($q,$d)=>$q->where('department',$d))
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))
            ->orderBy('name')
            ->paginate(config('biometric.per_page',25));

        $departments = $this->model()::distinct()->pluck('department')->filter()->sort();
        return view('biometric::employees.index', compact('employees','departments'));
    }

    public function create()
    {
        $shifts = app(config('biometric.models.shift'))::orderBy('name')->get();
        return view('biometric::employees.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => 'required|string|max:50|unique:biometric_employees,employee_code',
            'name'          => 'required|string|max:100',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'department'    => 'nullable|string|max:100',
            'designation'   => 'nullable|string|max:100',
            'join_date'     => 'nullable|date',
            'zk_user_id'    => 'nullable|integer|min:1|max:65535',
            'card_number'   => 'nullable|string|max:50',
            'status'        => 'required|in:active,inactive',
            'shift_id'      => 'nullable|exists:biometric_shifts,id',
        ]);

        $employee = $this->model()::create(array_merge(
            \Illuminate\Support\Arr::except($data,['shift_id']),
            ['sync_status'=>'pending']
        ));

        if ($request->shift_id) {
            $employee->shifts()->attach($request->shift_id, ['effective_from'=>now()->toDateString()]);
        }

        return redirect()->route('biometric.employees.index')->with('success','Employee registered successfully.');
    }

    public function show($id)
    {
        $employee = $this->model()::with(['shifts','attendanceLogs'=>fn($q)=>$q->latest('work_date')->limit(30),'leaveRequests.leaveType'])->findOrFail($id);
        return view('biometric::employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = $this->model()::findOrFail($id);
        $shifts   = app(config('biometric.models.shift'))::orderBy('name')->get();
        return view('biometric::employees.edit', compact('employee','shifts'));
    }

    public function update(Request $request, $id)
    {
        $employee = $this->model()::findOrFail($id);
        $data = $request->validate([
            'employee_code' => 'required|string|max:50|unique:biometric_employees,employee_code,'.$id,
            'name'          => 'required|string|max:100',
            'email'         => 'nullable|email',
            'phone'         => 'nullable|string|max:20',
            'department'    => 'nullable|string|max:100',
            'designation'   => 'nullable|string|max:100',
            'join_date'     => 'nullable|date',
            'zk_user_id'    => 'nullable|integer|min:1|max:65535',
            'card_number'   => 'nullable|string|max:50',
            'status'        => 'required|in:active,inactive',
            'shift_id'      => 'nullable|exists:biometric_shifts,id',
        ]);

        $employee->update(array_merge(\Illuminate\Support\Arr::except($data,['shift_id']),['sync_status'=>'pending']));

        if ($request->filled('shift_id')) {
            $employee->shifts()->wherePivotNull('effective_to')->each(fn($s)=>$s->pivot->update(['effective_to'=>now()->toDateString()]));
            $employee->shifts()->attach($request->shift_id,['effective_from'=>now()->toDateString()]);
        }

        return redirect()->route('biometric.employees.show',$id)->with('success','Employee updated.');
    }

    public function destroy($id)
    {
        $this->model()::findOrFail($id)->delete(); // soft delete
        return redirect()->route('biometric.employees.index')->with('success','Employee removed.');
    }
}

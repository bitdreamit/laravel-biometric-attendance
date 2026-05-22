<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShiftController extends Controller
{
    private function model() { return app(config('biometric.models.shift')); }

    public function index()
    {
        $shifts = $this->model()::withCount('employees')->orderBy('start_time')->get();
        return view('biometric::shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('biometric::shifts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100|unique:biometric_shifts,name',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i',
            'grace_late'     => 'nullable|integer|min:0|max:120',
            'grace_early_out'=> 'nullable|integer|min:0|max:120',
            'overtime_after' => 'nullable|integer|min:0|max:240',
            'is_overnight'   => 'nullable|boolean',
            'is_flexible'    => 'nullable|boolean',
            'working_days'   => 'required|array|min:1',
            'working_days.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $data['working_days'] = implode(',', $request->working_days);
        $data['is_overnight'] = $request->boolean('is_overnight');
        $data['is_flexible']  = $request->boolean('is_flexible');

        $this->model()::create($data);
        return redirect()->route('biometric.shifts.index')->with('success','Shift created.');
    }

    public function edit($id)
    {
        $shift = $this->model()::findOrFail($id);
        return view('biometric::shifts.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $shift = $this->model()::findOrFail($id);
        $data  = $request->validate([
            'name'           => 'required|string|max:100|unique:biometric_shifts,name,'.$id,
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i',
            'grace_late'     => 'nullable|integer|min:0|max:120',
            'grace_early_out'=> 'nullable|integer|min:0|max:120',
            'overtime_after' => 'nullable|integer|min:0|max:240',
            'is_overnight'   => 'nullable|boolean',
            'is_flexible'    => 'nullable|boolean',
            'working_days'   => 'required|array|min:1',
        ]);

        $data['working_days'] = implode(',', $request->working_days);
        $data['is_overnight'] = $request->boolean('is_overnight');
        $data['is_flexible']  = $request->boolean('is_flexible');

        $shift->update($data);
        return redirect()->route('biometric.shifts.index')->with('success','Shift updated.');
    }

    public function destroy($id)
    {
        $this->model()::findOrFail($id)->delete();
        return redirect()->route('biometric.shifts.index')->with('success','Shift deleted.');
    }
}

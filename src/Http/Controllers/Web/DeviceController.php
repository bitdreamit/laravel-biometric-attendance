<?php
namespace Bitdreamit\BiometricAttendance\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeviceController extends Controller
{
    private function model() { return app(config('biometric.models.device')); }

    public function index()
    {
        $devices = $this->model()::withCount([
            'attendance as today_punches' => fn($q)=>$q->whereDate('punched_at',now()->toDateString())
        ])->orderBy('name')->get();
        return view('biometric::devices.index', compact('devices'));
    }

    public function create() { return view('biometric::devices.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|unique:biometric_devices,serial_number',
            'name'          => 'required|string|max:100',
            'ip'            => 'nullable|ip',
            'port'          => 'nullable|integer|min:1|max:65535',
            'location'      => 'nullable|string|max:100',
        ]);
        $this->model()::create($request->only('serial_number','name','ip','port','location'));
        return redirect()->route('biometric.devices.index')->with('success','Device added.');
    }

    public function edit($id)
    {
        $device = $this->model()::findOrFail($id);
        return view('biometric::devices.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $device = $this->model()::findOrFail($id);
        $request->validate(['name'=>'required|string','ip'=>'nullable|ip','port'=>'nullable|integer','location'=>'nullable|string']);
        $device->update($request->only('name','ip','port','location','is_active'));
        return redirect()->route('biometric.devices.index')->with('success','Device updated.');
    }

    public function destroy($id) {
        $this->model()::findOrFail($id)->update(['is_active'=>false]);
        return redirect()->route('biometric.devices.index')->with('success','Device disabled.');
    }
}

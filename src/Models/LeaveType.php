<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model {
    protected $table = 'biometric_leave_types';
    protected $fillable = ['tenant_id','name','days_allowed','is_paid','carry_forward'];
    protected $casts = ['is_paid'=>'boolean','carry_forward'=>'boolean'];

    public function requests() { return $this->hasMany(config('biometric.models.leave_request')); }

    public function usedDays(int $employeeId, int $year): int {
        return $this->requests()
            ->where('employee_id',$employeeId)
            ->where('status','approved')
            ->whereYear('from_date',$year)
            ->sum('days');
    }
}

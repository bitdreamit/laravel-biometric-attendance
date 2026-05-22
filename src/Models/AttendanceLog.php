<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model {
    protected $table = 'biometric_attendance_logs';
    protected $fillable = ['tenant_id','employee_id','shift_id','work_date','check_in','check_out','working_minutes','late_minutes','early_out_minutes','overtime_minutes','status','remarks'];
    protected $casts = ['work_date'=>'date','check_in'=>'datetime','check_out'=>'datetime'];

    public function employee() { return $this->belongsTo(config('biometric.models.employee')); }
    public function shift()    { return $this->belongsTo(config('biometric.models.shift')); }

    public function workingHours(): string {
        if (!$this->working_minutes) return '0h 0m';
        return floor($this->working_minutes/60).'h '.($this->working_minutes%60).'m';
    }
}

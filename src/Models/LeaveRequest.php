<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model {
    protected $table = 'biometric_leave_requests';
    protected $fillable = ['tenant_id','employee_id','leave_type_id','from_date','to_date','days','reason','status','approved_by','actioned_at'];
    protected $casts = ['from_date'=>'date','to_date'=>'date','actioned_at'=>'datetime'];

    public function employee()  { return $this->belongsTo(config('biometric.models.employee')); }
    public function leaveType() { return $this->belongsTo(config('biometric.models.leave_type')); }

    public function approve(string $by = null): bool {
        return $this->update(['status'=>'approved','approved_by'=>$by??auth()->user()?->name,'actioned_at'=>now()]);
    }
    public function reject(string $by = null): bool {
        return $this->update(['status'=>'rejected','approved_by'=>$by??auth()->user()?->name,'actioned_at'=>now()]);
    }
}

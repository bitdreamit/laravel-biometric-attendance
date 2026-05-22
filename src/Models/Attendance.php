<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $table = 'biometric_attendance';
    protected $fillable = ['tenant_id','zk_user_id','employee_id','employee_code','punched_at','device_sn','device_ip','punch_type','verify_type','source','sync_status'];
    protected $casts = ['punched_at'=>'datetime'];

    public function employee() { return $this->belongsTo(config('biometric.models.employee')); }
    public function scopeForTenant($q,$tid) { return $tid ? $q->where('tenant_id',$tid) : $q; }
    public function scopePending($q) { return $q->where('sync_status','received'); }
    public function scopeOnDate($q,$date) { return $q->whereDate('punched_at',$date); }
}

<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'biometric_employees';
    protected $fillable = [
        'tenant_id','user_id','employee_code','name','email','phone',
        'department','designation','join_date','zk_user_id','card_number',
        'status','sync_status','synced_at',
    ];
    protected $casts = ['join_date'=>'date','synced_at'=>'datetime'];

    public function scopeActive($q)         { return $q->where('status','active'); }
    public function scopeForTenant($q,$tid) { return $tid ? $q->where('tenant_id',$tid) : $q; }
    public function scopePendingSync($q)    { return $q->where('sync_status','pending'); }

    public function user()           { return $this->belongsTo(\App\Models\User::class); }
    public function shifts()         { return $this->belongsToMany(config('biometric.models.shift'),'biometric_employee_shifts','employee_id','shift_id')->withPivot('effective_from','effective_to')->withTimestamps(); }
    public function attendance()     { return $this->hasMany(config('biometric.models.attendance')); }
    public function attendanceLogs() { return $this->hasMany(config('biometric.models.attendance_log')); }
    public function leaveRequests()  { return $this->hasMany(config('biometric.models.leave_request')); }

    public function currentShift(\DateTime $date = null) {
        $date = ($date ?? now())->format('Y-m-d');
        return $this->shifts()
            ->wherePivot('effective_from','<=',$date)
            ->where(fn($q)=>$q->wherePivotNull('effective_to')->orWherePivot('effective_to','>=',$date))
            ->orderByPivot('effective_from','desc')
            ->first();
    }

    // Serialize for offline agent response
    public function toAgentArray(): array {
        return [
            'id'            => (string)$this->id,
            'employee_code' => $this->employee_code,
            'name'          => $this->name,
            'zk_user_id'    => $this->zk_user_id,
            'card_number'   => $this->card_number,
            'department'    => $this->department,
            'designation'   => $this->designation,
            'status'        => $this->status,
            'tenant_id'     => $this->tenant_id,
        ];
    }
}

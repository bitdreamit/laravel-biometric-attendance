<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model {
    use HasFactory;
    protected $table = 'biometric_devices';
    protected $fillable = ['tenant_id','serial_number','name','ip','port','location','is_active','last_seen_at'];
    protected $casts = ['is_active'=>'boolean','last_seen_at'=>'datetime'];
    public function scopeActive($q)        { return $q->where('is_active', true); }
    public function scopeForTenant($q,$tid){ return $tid ? $q->where('tenant_id',$tid) : $q; }
    public function attendance()           { return $this->hasMany(config('biometric.models.attendance'), 'device_sn', 'serial_number'); }
}

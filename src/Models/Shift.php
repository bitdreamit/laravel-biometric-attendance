<?php
namespace Bitdreamit\BiometricAttendance\Models;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model {
    protected $table = 'biometric_shifts';
    protected $fillable = ['tenant_id','name','start_time','end_time','grace_late','grace_early_out','overtime_after','is_overnight','is_flexible','working_days'];
    protected $casts = ['is_overnight'=>'boolean','is_flexible'=>'boolean'];

    public function employees()    { return $this->belongsToMany(config('biometric.models.employee'),'biometric_employee_shifts','shift_id','employee_id')->withPivot('effective_from','effective_to'); }
    public function workingDaysArray(): array { return explode(',',$this->working_days); }
    public function isWorkingDay(string $dayName): bool { return in_array($dayName, $this->workingDaysArray()); }

    public function calculateMinutes(string $checkIn, string $checkOut): array {
        $in      = \Carbon\Carbon::parse($checkIn);
        $out     = \Carbon\Carbon::parse($checkOut);
        $start   = \Carbon\Carbon::parse($this->start_time)->setDateFrom($in);
        $end     = \Carbon\Carbon::parse($this->end_time)->setDateFrom($in);
        if ($this->is_overnight && $end <= $start) $end->addDay();

        $working  = max(0, $in->diffInMinutes($out));
        $late     = $this->is_flexible ? 0 : max(0, $in->diffInMinutes($start, false) * -1 - $this->grace_late);
        $earlyOut = $this->is_flexible ? 0 : max(0, $out->diffInMinutes($end, false) - $this->grace_early_out);
        $overtime = $this->is_flexible ? 0 : max(0, $out->diffInMinutes($end, false) * -1 - $this->overtime_after);

        return [
            'working_minutes'   => $working,
            'late_minutes'      => max(0, (int)$late),
            'early_out_minutes' => max(0, (int)$earlyOut),
            'overtime_minutes'  => max(0, (int)$overtime),
        ];
    }
}

<?php
namespace Bitdreamit\BiometricAttendance\Services;

use Carbon\Carbon;

class AttendanceProcessorService
{
    public function processDate(string $date): int
    {
        $empModel = app(config('biometric.models.employee'));
        $count    = 0;

        $empModel::active()->each(function ($employee) use ($date, &$count) {
            $this->processForEmployee($employee, $date);
            $count++;
        });

        return $count;
    }

    public function processForEmployee(object $employee, string $date): void
    {
        $attModel = app(config('biometric.models.attendance'));
        $logModel = app(config('biometric.models.attendance_log'));

        $punches = $attModel::where('employee_id', $employee->id)
            ->whereDate('punched_at', $date)
            ->orderBy('punched_at')
            ->get();

        $shift = $employee->currentShift(Carbon::parse($date));

        if ($punches->isEmpty()) {
            // Mark absent unless on approved leave
            $onLeave = $this->hasApprovedLeave($employee->id, $date);
            $logModel::updateOrCreate(
                ['employee_id' => $employee->id, 'work_date' => $date],
                ['status' => $onLeave ? 'on_leave' : 'absent', 'shift_id' => $shift?->id]
            );
            return;
        }

        $checkIn  = $punches->first()->punched_at;
        $checkOut = $punches->count() > 1 ? $punches->last()->punched_at : null;

        // Default calculations
        $late = $earlyOut = $overtime = $working = 0;
        $status = 'present';

        if ($shift && $checkIn && $checkOut) {
            $calc    = $shift->calculateMinutes($checkIn, $checkOut);
            $late    = $calc['late_minutes'];
            $earlyOut= $calc['early_out_minutes'];
            $overtime= $calc['overtime_minutes'];
            $working = $calc['working_minutes'];

            // Half day: worked less than 50% of shift
            $shiftStart = Carbon::parse($shift->start_time)->setDateFrom($checkIn);
            $shiftEnd   = Carbon::parse($shift->end_time)->setDateFrom($checkIn);
            if ($shift->is_overnight && $shiftEnd <= $shiftStart) $shiftEnd->addDay();
            $shiftTotal = $shiftStart->diffInMinutes($shiftEnd);
            if ($shiftTotal > 0 && $working < ($shiftTotal * 0.5)) {
                $status = 'half_day';
            }
        } elseif ($checkIn && $checkOut) {
            $working = $checkIn->diffInMinutes($checkOut);
        }

        if ($this->hasApprovedLeave($employee->id, $date)) {
            $status = 'on_leave';
        }

        $logModel::updateOrCreate(
            ['employee_id' => $employee->id, 'work_date' => $date],
            [
                'shift_id'          => $shift?->id,
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'working_minutes'   => $working,
                'late_minutes'      => $late,
                'early_out_minutes' => $earlyOut,
                'overtime_minutes'  => $overtime,
                'status'            => $status,
            ]
        );

        // Update punch types on raw records
        $punches->first()?->update(['punch_type' => 'check_in']);
        if ($punches->count() > 1) {
            $punches->last()?->update(['punch_type' => 'check_out']);
        }
    }

    private function hasApprovedLeave(int $employeeId, string $date): bool
    {
        return app(config('biometric.models.leave_request'))::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('from_date', '<=', $date)
            ->where('to_date',   '>=', $date)
            ->exists();
    }
}

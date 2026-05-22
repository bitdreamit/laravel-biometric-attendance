<?php
namespace Bitdreamit\BiometricAttendance\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService processor()
 * @see \Bitdreamit\BiometricAttendance\BiometricManager
 */
class Biometric extends Facade {
    protected static function getFacadeAccessor(): string { return 'biometric'; }
}

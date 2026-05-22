<?php
namespace Bitdreamit\BiometricAttendance;

use Illuminate\Foundation\Application;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;

class BiometricManager {
    public function __construct(protected Application $app) {}
    public function processor(): AttendanceProcessorService {
        return $this->app->make(AttendanceProcessorService::class);
    }
    public function model(string $key): object {
        return $this->app->make(config("biometric.models.$key"));
    }
}

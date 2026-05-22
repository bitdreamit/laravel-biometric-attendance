<?php
namespace Bitdreamit\BiometricAttendance\Console\Commands;

use Illuminate\Console\Command;
use Bitdreamit\BiometricAttendance\Services\AttendanceProcessorService;

class ProcessAttendanceCommand extends Command
{
    protected $signature   = 'biometric:process {date? : Y-m-d, defaults to today}';
    protected $description = 'Process raw punches into attendance log (IN/OUT, late, overtime)';

    public function handle(AttendanceProcessorService $processor): void
    {
        $date  = $this->argument('date') ?? now()->toDateString();
        $this->info("Processing attendance for $date...");
        $count = $processor->processDate($date);
        $this->info("✓ Processed $count employees.");
    }
}

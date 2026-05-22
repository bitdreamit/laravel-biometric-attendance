<?php
namespace Bitdreamit\BiometricAttendance\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature   = 'biometric:install';
    protected $description = 'Install the Biometric Attendance package';

    public function handle(): void
    {
        $this->info('Installing Biometric Attendance...');
        $this->call('vendor:publish', ['--tag'=>'biometric-config',     '--force'=>true]);
        $this->call('vendor:publish', ['--tag'=>'biometric-migrations', '--force'=>true]);
        $this->call('migrate');
        $this->info('');
        $this->info('✓ Biometric Attendance installed.');
        $this->info('  Dashboard: /biometric');
        $this->info('  API docs:  /api/biometric/employees');
        $this->info('  Generate agent token: php artisan biometric:agent-token');
    }
}

<?php
namespace Bitdreamit\BiometricAttendance\Console\Commands;

use Illuminate\Console\Command;

/**
 * php artisan biometric:sync-employees
 *
 * Marks ALL active employees as sync_status=pending so the offline agent
 * picks them all up on its next poll cycle.
 * Useful after bulk imports or when a new device comes online.
 */
class SyncEmployeesCommand extends Command
{
    protected $signature   = 'biometric:sync-employees
                                {--tenant= : Scope to a specific tenant_id}
                                {--employee= : Sync a single employee by code}';

    protected $description = 'Mark employees as pending so the agent re-syncs them to ZKTeco devices';

    public function handle(): void
    {
        $model    = app(config('biometric.models.employee'));
        $tenantId = $this->option('tenant');
        $empCode  = $this->option('employee');

        $query = $model::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($empCode,  fn($q) => $q->where('employee_code', $empCode))
            ->where('status', 'active');

        $count = $query->count();

        if ($count === 0) {
            $this->warn('No matching employees found.');
            return;
        }

        $this->info("Marking $count employee(s) as pending sync...");
        $query->update(['sync_status' => 'pending', 'synced_at' => null]);

        $this->info("✓ Done. The agent will pick these up on its next poll.");
        $this->line("  The agent polls every " . config('biometric.agent.poll_interval', 300) . " seconds.");
    }
}

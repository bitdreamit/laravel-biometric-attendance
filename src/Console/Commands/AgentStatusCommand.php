<?php
namespace Bitdreamit\BiometricAttendance\Console\Commands;

use Illuminate\Console\Command;

/**
 * php artisan biometric:agent-status
 *
 * Shows the last heartbeat received from each offline agent,
 * device connection status, and queue depth.
 */
class AgentStatusCommand extends Command
{
    protected $signature   = 'biometric:agent-status {--tenant= : Filter by tenant_id}';
    protected $description = 'Show the last-known status of offline agents (from heartbeat data)';

    public function handle(): void
    {
        $devModel = app(config('biometric.models.device'));
        $tenantId = $this->option('tenant');

        // Devices table
        $devices = $devModel::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($devices->isEmpty()) {
            $this->warn('No active devices found. Add devices at /biometric/devices');
            return;
        }

        $this->info('ZKTeco Device Status:');
        $headers = ['Name', 'Serial', 'IP', 'Last Seen', 'Status'];
        $rows    = $devices->map(function ($d) {
            $lastSeen = $d->last_seen_at
                ? $d->last_seen_at->diffForHumans()
                : 'Never';
            $isOnline  = $d->last_seen_at && $d->last_seen_at->gt(now()->subMinutes(15));
            $status    = $isOnline ? '<fg=green>Online</>' : '<fg=red>Offline/Unknown</>';
            return [$d->name, $d->serial_number, $d->ip ?? '—', $lastSeen, $status];
        })->toArray();

        $this->table($headers, $rows);

        // Agent heartbeat from cache
        $cacheKey = 'biometric_agent_heartbeat' . ($tenantId ? ":$tenantId" : '');
        if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
            $hb = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($hb) {
                $this->newLine();
                $this->info('Last Agent Heartbeat:');
                $this->line("  Last seen    : {$hb['last_seen']}");
                $this->line("  Agent version: {$hb['agent_version']}");
                $q = $hb['queue'] ?? [];
                $this->line("  Queue        : pending={$q['pending']} synced={$q['synced']} error={$q['error']}");
            } else {
                $this->newLine();
                $this->warn('No heartbeat received yet. Is the agent running?');
                $this->line('  Enable heartbeat in agent config: server.sync_enabled = true');
            }
        }
    }
}

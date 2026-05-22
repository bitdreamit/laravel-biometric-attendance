<?php
namespace Bitdreamit\BiometricAttendance\Console\Commands;

use Illuminate\Console\Command;

class SyncAgentCommand extends Command
{
    protected $signature   = 'biometric:agent-token {name=biometric-agent}';
    protected $description = 'Generate a Sanctum token for the offline Python agent';

    public function handle(): void
    {
        if (!class_exists(\Laravel\Sanctum\HasApiTokens::class)) {
            $this->error('Laravel Sanctum is required. Run: composer require laravel/sanctum');
            return;
        }
        $user = \App\Models\User::first();
        if (!$user) { $this->error('No users found. Create a user first.'); return; }
        $token = $user->createToken($this->argument('name'))->plainTextToken;
        $this->info('Agent API token generated:');
        $this->line('');
        $this->line($token);
        $this->line('');
        $this->info('Add to your offline agent config:');
        $this->line('  server.api_key = '.$token);
    }
}

# laravel-biometric-attendance

> **Packagist:** `bitdreamit/laravel-biometric-attendance`
> **Offline agent:** [bitdreamit/AdvancedBiometricAttendance](https://github.com/bitdreamit/AdvancedBiometricAttendance)
> **Integration contract:** [INTEGRATION.md](./INTEGRATION.md)

Laravel package for ZKTeco biometric attendance management.
Bidirectional sync with the offline Python agent via a REST API.

---

## Installation

```bash
composer require bitdreamit/laravel-biometric-attendance
php artisan biometric:install
```

Migrations run, config published, dashboard available at `/biometric`.

---

## Quick Start

### 1 — Generate agent API token

```bash
php artisan biometric:agent-token
```

Copy the token to the offline agent `config/default_config.json`:

```json
"server": {
  "url":     "https://your-laravel-app.com/",
  "api_key": "PASTE_TOKEN_HERE"
}
```

> Tokens are validated by `AgentTokenMiddleware` — only tokens created by
> `biometric:agent-token` are accepted on the API endpoints.

### 2 — Open the dashboard

```
https://your-app.com/biometric
```

### 3 — Schedule daily attendance processing

```php
// routes/console.php (Laravel 11+)
Schedule::command('biometric:process')->daily();

// app/Console/Kernel.php (Laravel 10)
$schedule->command('biometric:process')->daily();
```

---

## API Endpoints (consumed by Python agent)

All endpoints require `Authorization: Bearer {token}` and pass through
`AgentTokenMiddleware` (agent-only token validation + 1000 req/min rate limit).

| Method | URL | Purpose |
|--------|-----|---------|
| GET | `/api/biometric/employees` | Agent pulls employee list |
| POST | `/api/biometric/attendance` | Agent pushes raw punches |
| POST | `/api/biometric/heartbeat` | Agent reports health + device status |
| GET | `/api/biometric/agent-commands` | Agent polls for pending commands |

See [INTEGRATION.md](./INTEGRATION.md) for exact request/response format.

---

## Dashboard Routes

| Route | URL | Description |
|-------|-----|-------------|
| `biometric.dashboard` | `/biometric` | Stats + agent status + device panel |
| `biometric.devices.*` | `/biometric/devices` | Device management |
| `biometric.employees.*` | `/biometric/employees` | Employee CRUD + shift assign |
| `biometric.employees.sync` | `POST /biometric/employees/{id}/sync` | Force re-sync to device |
| `biometric.shifts.*` | `/biometric/shifts` | Shift definitions |
| `biometric.attendance.index` | `/biometric/attendance` | Daily attendance log |
| `biometric.attendance.punches` | `/biometric/attendance/punches` | Raw punch records |
| `biometric.leave.*` | `/biometric/leave` | Leave apply / approve |
| `biometric.reports.daily` | `/biometric/reports/daily` | Daily report + CSV/JSON |
| `biometric.reports.monthly` | `/biometric/reports/monthly` | Monthly summary |

---

## Artisan Commands

| Command | Description |
|---------|-------------|
| `biometric:install` | Run migrations, publish config |
| `biometric:agent-token` | Generate Sanctum token for Python agent |
| `biometric:process {date}` | Process raw punches → attendance log |
| `biometric:sync-employees` | Mark all employees pending → agent re-syncs to device |
| `biometric:sync-employees --employee=EMP001` | Re-sync a single employee |
| `biometric:sync-employees --tenant=abc` | Re-sync all employees for a tenant |
| `biometric:agent-status` | Show device last-seen times + agent heartbeat |

---

## Configuration

```bash
php artisan vendor:publish --tag=biometric-config
```

### Custom layout

```php
// config/biometric.php
'layout' => 'layouts.app',   // your Blade layout (yield 'content','styles','scripts')
```

### Tailwind / Filament

```php
'layout'      => 'layouts.app',
'theme'       => 'tailwind',
'web_enabled' => false,   // disable web routes, use your own Filament resources
```

### Multi-tenancy

```php
'tenant_model'  => \App\Models\Company::class,
'tenant_column' => 'company_id',
'tenant_scope'  => true,
```

### Swap any model

```php
'models' => [
    'employee' => \App\Models\StaffMember::class,
],
```

---

## Database Tables

| Table | Purpose |
|-------|---------|
| `biometric_devices` | ZKTeco hardware |
| `biometric_employees` | Staff (synced to/from offline agent) |
| `biometric_shifts` | Shift definitions |
| `biometric_employee_shifts` | Employee ↔ shift with effective dates |
| `biometric_attendance` | Raw punches from agent |
| `biometric_attendance_logs` | Processed IN/OUT with hours, late, OT |
| `biometric_leave_types` | Annual, Sick, Casual, etc. |
| `biometric_leave_requests` | Applications with approve/reject |

---

## Pushing commands to the agent

Queue a command from anywhere in your Laravel code:

```php
use Bitdreamit\BiometricAttendance\Http\Controllers\Api\AgentCommandController;

// Force immediate sync
AgentCommandController::queue('sync_now', [], $tenantId);

// Clear device log
AgentCommandController::queue('clear_device_log', ['device_sn' => 'ABC123'], $tenantId);

// Enroll a user on a device
AgentCommandController::queue('enroll_user', [
    'device_sn' => 'ABC123',
    'employee'  => ['zk_user_id' => '5', 'name' => 'Alice', 'card_number' => null],
], $tenantId);
```

The agent picks these up on its next poll of `GET /api/biometric/agent-commands`.

---

## Sync Flow

```
Online (this package)                     Offline (Python agent)
─────────────────────                     ──────────────────────
GET /api/biometric/employees ───────────► pulls employee list every 5 min
                                          stores in local SQLite
                                          pushes user record to ZK device

POST /api/biometric/attendance ◄───────── pushes raw punches every 5 min
auto-processes to biometric_attendance_logs

POST /api/biometric/heartbeat ◄───────── reports device status every 5 min
updates biometric_devices.last_seen_at
dashboard shows online/offline status

GET /api/biometric/agent-commands ◄────── agent polls for commands
executes: sync_now, clear_device_log, enroll_user
```

---

## License

MIT — [bitdreamit](https://github.com/bitdreamit)

# laravel-biometric-attendance

> **Packagist:** `bitdreamit/laravel-biometric-attendance`

Laravel package for ZKTeco biometric attendance management.
Works with the [offline Python agent](https://github.com/bitdreamit/AdvancedBiometricAttendance)
via a two-endpoint REST API.

---

## Installation

```bash
composer require bitdreamit/laravel-biometric-attendance
php artisan biometric:install
```

That's it. Migrations run, config is published, dashboard is available at `/biometric`.

---

## Quick Start

### 1 — Generate agent API token

```bash
php artisan biometric:agent-token
```

Copy the token into your offline Python agent `config/default_config.json`:
```json
"server": {
  "url":     "https://your-laravel-app.com/",
  "api_key": "PASTE_TOKEN_HERE"
}
```

### 2 — Open the dashboard

```
https://your-app.com/biometric
```

### 3 — Process attendance daily (add to scheduler)

```php
// app/Console/Kernel.php
$schedule->command('biometric:process')->daily();
```

---

## API Endpoints (used by Python agent)

| Method | URL | Purpose |
|--------|-----|---------|
| GET | `/api/biometric/employees` | Agent pulls employee list |
| POST | `/api/biometric/attendance` | Agent pushes raw punches |

Authentication: `Authorization: Bearer {token}` (Laravel Sanctum)

---

## Dashboard Routes

| Route | URL | Description |
|-------|-----|-------------|
| `biometric.dashboard` | `/biometric` | Overview stats + recent punches |
| `biometric.devices.*` | `/biometric/devices` | Device management |
| `biometric.employees.*` | `/biometric/employees` | Employee CRUD + shift assign |
| `biometric.shifts.*` | `/biometric/shifts` | Shift definitions |
| `biometric.attendance.index` | `/biometric/attendance` | Daily attendance log |
| `biometric.attendance.punches` | `/biometric/attendance/punches` | Raw punch records |
| `biometric.leave.*` | `/biometric/leave` | Leave apply / approve |
| `biometric.reports.daily` | `/biometric/reports/daily` | Daily report + CSV/JSON export |
| `biometric.reports.monthly` | `/biometric/reports/monthly` | Monthly summary |

---

## Configuration

Publish and edit `config/biometric.php`:

```bash
php artisan vendor:publish --tag=biometric-config
```

### Use your own layout (custom theme)

```php
// config/biometric.php
'layout' => 'layouts.app',   // your Blade layout
```

Your layout must `@yield('content')`, `@yield('styles')`, `@yield('scripts')`.

### Tailwind / Filament users

```php
'layout'  => 'layouts.app',   // your layout
'theme'   => 'tailwind',       // tells package not to load Bootstrap
'web_enabled' => false,        // optional: disable web routes, use Filament resources
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
    'employee' => \App\Models\StaffMember::class,  // your own model
],
```

Your model must implement the same fillable fields and `toAgentArray()` method.

---

## Database Tables

All tables are prefixed `biometric_`:

| Table | Purpose |
|-------|---------|
| `biometric_devices` | ZKTeco hardware |
| `biometric_employees` | Staff (synced to offline agent) |
| `biometric_shifts` | Shift definitions |
| `biometric_employee_shifts` | Employee ↔ shift with effective dates |
| `biometric_attendance` | Raw punches from agent |
| `biometric_attendance_logs` | Processed IN/OUT with hours, late, OT |
| `biometric_leave_types` | Annual, Sick, Casual, etc. |
| `biometric_leave_requests` | Applications with approve/reject |

---

## Artisan Commands

| Command | Description |
|---------|-------------|
| `biometric:install` | Run migrations, publish config |
| `biometric:agent-token` | Generate Sanctum token for Python agent |
| `biometric:process {date}` | Process raw punches → attendance log |

---

## Sync Flow

```
Online (this package)                    Offline (Python agent)
─────────────────────                    ──────────────────────
GET /api/biometric/employees ──────────► pulls employee list
                                         stores locally in SQLite
                                         pushes user record to ZK device

POST /api/biometric/attendance ◄──────── pushes raw punches every 5 min
stores in biometric_attendance
auto-processes to biometric_attendance_logs (if config auto_process=true)
```

---

## Shared Columns (online ↔ offline)

| Column | Purpose |
|--------|---------|
| `remote_id` / `id` | Link offline employee to online record |
| `zk_user_id` | Integer ID on ZKTeco device (1–65535) |
| `employee_code` | Human-readable employee ID |
| `card_number` | RFID card number |
| `tenant_id` | Multi-tenancy scope |
| `punched_at` | Exact punch timestamp |
| `device_sn` | Which device recorded the punch |
| `verify_type` | fingerprint / face / card / pin |

---

## License

MIT — [bitdreamit](https://github.com/bitdreamit)

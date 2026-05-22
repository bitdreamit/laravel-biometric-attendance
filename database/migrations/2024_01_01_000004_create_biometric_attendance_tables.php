<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('biometric_attendance', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->string('zk_user_id')->index();
            $t->foreignId('employee_id')->nullable()->constrained('biometric_employees')->nullOnDelete();
            $t->string('employee_code')->nullable();
            $t->timestamp('punched_at')->index();
            $t->string('device_sn')->nullable();
            $t->string('device_ip')->nullable();
            $t->enum('punch_type',['check_in','check_out','unknown'])->default('unknown');
            $t->enum('verify_type',['fingerprint','face','card','pin','password','other'])->default('fingerprint');
            $t->string('source')->default('agent');
            $t->string('sync_status')->default('received');
            $t->timestamps();
            $t->unique(['zk_user_id','device_sn','punched_at'],'biometric_punch_unique');
        });

        Schema::create('biometric_attendance_logs', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->foreignId('employee_id')->constrained('biometric_employees')->cascadeOnDelete();
            $t->foreignId('shift_id')->nullable()->constrained('biometric_shifts')->nullOnDelete();
            $t->date('work_date');
            $t->timestamp('check_in')->nullable();
            $t->timestamp('check_out')->nullable();
            $t->unsignedSmallInteger('working_minutes')->nullable();
            $t->unsignedSmallInteger('late_minutes')->default(0);
            $t->unsignedSmallInteger('early_out_minutes')->default(0);
            $t->unsignedSmallInteger('overtime_minutes')->default(0);
            $t->enum('status',['present','absent','half_day','on_leave','holiday','weekend'])->default('present');
            $t->string('remarks')->nullable();
            $t->timestamps();
            $t->unique(['employee_id','work_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('biometric_attendance_logs');
        Schema::dropIfExists('biometric_attendance');
    }
};

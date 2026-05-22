<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biometric_shifts', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->string('name');
            $t->time('start_time');
            $t->time('end_time');
            $t->unsignedTinyInteger('grace_late')->default(15);
            $t->unsignedTinyInteger('grace_early_out')->default(10);
            $t->unsignedTinyInteger('overtime_after')->default(30);
            $t->boolean('is_overnight')->default(false);
            $t->boolean('is_flexible')->default(false);
            $t->string('working_days')->default('Mon,Tue,Wed,Thu,Fri');
            $t->timestamps();
            $t->unique(['name','tenant_id']);
        });

        Schema::create('biometric_employee_shifts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('employee_id')->constrained('biometric_employees')->cascadeOnDelete();
            $t->foreignId('shift_id')->constrained('biometric_shifts')->cascadeOnDelete();
            $t->date('effective_from');
            $t->date('effective_to')->nullable();
            $t->timestamps();
            $t->unique(['employee_id','effective_from']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('biometric_employee_shifts');
        Schema::dropIfExists('biometric_shifts');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biometric_leave_types', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->string('name');
            $t->unsignedTinyInteger('days_allowed')->default(0);
            $t->boolean('is_paid')->default(true);
            $t->boolean('carry_forward')->default(false);
            $t->timestamps();
            $t->unique(['name','tenant_id']);
        });

        Schema::create('biometric_leave_requests', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->foreignId('employee_id')->constrained('biometric_employees')->cascadeOnDelete();
            $t->foreignId('leave_type_id')->constrained('biometric_leave_types');
            $t->date('from_date');
            $t->date('to_date');
            $t->unsignedTinyInteger('days');
            $t->text('reason')->nullable();
            $t->enum('status',['pending','approved','rejected'])->default('pending');
            $t->string('approved_by')->nullable();
            $t->timestamp('actioned_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('biometric_leave_requests');
        Schema::dropIfExists('biometric_leave_types');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biometric_employees', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('employee_code');
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('department')->nullable();
            $t->string('designation')->nullable();
            $t->date('join_date')->nullable();
            $t->string('zk_user_id')->nullable()->comment('Integer ID on device 1-65535');
            $t->string('card_number')->nullable()->comment('RFID card');
            $t->enum('status', ['active','inactive'])->default('active');
            $t->string('sync_status')->default('pending'); // pending|synced|deleted
            $t->timestamp('synced_at')->nullable();
            $t->timestamps();
            $t->unique(['employee_code', 'tenant_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('biometric_employees'); }
};

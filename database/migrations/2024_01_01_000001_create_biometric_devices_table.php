<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biometric_devices', function (Blueprint $t) {
            $t->id();
            $t->string('tenant_id')->nullable()->index();
            $t->string('serial_number')->unique();
            $t->string('name');
            $t->string('ip')->nullable();
            $t->unsignedSmallInteger('port')->default(4370);
            $t->string('location')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamp('last_seen_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('biometric_devices'); }
};

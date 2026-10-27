<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('recorded_at');
            $table->unsignedSmallInteger('systolic_bp')->nullable(); // mmHg (Huyết áp tâm thu)
            $table->unsignedSmallInteger('diastolic_bp')->nullable(); // mmHg (Huyết áp tâm trương)
            $table->unsignedSmallInteger('heart_rate')->nullable(); // l/p (Mạch / Nhịp tim)
            $table->decimal('temperature', 4, 1)->nullable(); // °C (Thân nhiệt)
            $table->unsignedSmallInteger('spo2')->nullable(); // % (Nồng độ oxy trong máu)
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->string('notes', 255)->nullable(); // Ghi chú lúc đo
            $table->timestamps();

            $table->index(['patient_profile_id', 'recorded_at'], 'patient_vitals_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_vitals');
    }
};

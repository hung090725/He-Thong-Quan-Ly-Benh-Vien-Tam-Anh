<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Module 2 Advanced Features.
     */
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('cchn_number', 50)->nullable()->after('qualification');
            $table->date('cchn_issued_date')->nullable()->after('cchn_number');
            $table->date('cchn_expiry_date')->nullable()->after('cchn_issued_date');
            $table->string('cchn_issuer', 100)->nullable()->default('Bộ Y Tế')->after('cchn_expiry_date');
            $table->decimal('rating_avg', 3, 2)->default(5.00)->after('is_available');
            $table->unsignedInteger('rating_count')->default(1)->after('rating_avg');
            $table->string('building', 50)->default('Tòa A')->after('room_number');
            $table->string('floor', 20)->default('Tầng 2')->after('building');
            $table->string('qualification_en', 100)->nullable()->after('bio');
            $table->text('bio_en')->nullable()->after('qualification_en');
        });

        Schema::table('specialties', function (Blueprint $table) {
            $table->string('emergency_readiness_level', 50)->default('standard')->after('is_active');
            $table->boolean('is_code_red_supported')->default(false)->after('emergency_readiness_level');
            $table->string('emergency_hotline', 20)->nullable()->after('is_code_red_supported');
        });

        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment');
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        Schema::create('doctor_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_audit_logs');
        Schema::dropIfExists('doctor_reviews');

        Schema::table('specialties', function (Blueprint $table) {
            $table->dropColumn(['emergency_readiness_level', 'is_code_red_supported', 'emergency_hotline']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn([
                'cchn_number',
                'cchn_issued_date',
                'cchn_expiry_date',
                'cchn_issuer',
                'rating_avg',
                'rating_count',
                'building',
                'floor',
                'qualification_en',
                'bio_en',
            ]);
        });
    }
};

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
        Schema::create('sick_leave_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code', 30)->unique();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('patient_profile_id')->constrained('patient_profiles')->onDelete('restrict');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('restrict');
            $table->foreignId('issued_by_user_id')->constrained('users')->onDelete('restrict');
            $table->string('employer_name', 255)->nullable();
            $table->string('diagnosis', 255);
            $table->string('treatment_method', 150)->default('Điều trị ngoại trú');
            $table->unsignedInteger('leave_days');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('father_mother_name', 150)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sick_leave_certificates');
    }
};

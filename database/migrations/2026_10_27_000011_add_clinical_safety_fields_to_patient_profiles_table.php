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
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('insurance_code');
            $table->string('allergies', 255)->nullable()->after('blood_group');
            $table->text('medical_history')->nullable()->after('allergies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn(['blood_group', 'allergies', 'medical_history']);
        });
    }
};

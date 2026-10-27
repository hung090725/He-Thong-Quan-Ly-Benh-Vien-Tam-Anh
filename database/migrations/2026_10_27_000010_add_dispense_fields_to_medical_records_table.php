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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->timestamp('dispensed_at')->nullable()->after('follow_up_date');
            $table->foreignId('dispensed_by_user_id')->nullable()->after('dispensed_at')->constrained('users')->nullOnDelete();
            $table->string('dispense_notes', 255)->nullable()->after('dispensed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['dispensed_by_user_id']);
            $table->dropColumn(['dispensed_at', 'dispensed_by_user_id', 'dispense_notes']);
        });
    }
};

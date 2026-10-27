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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('medication_code', 30)->unique();
            $table->string('name', 150)->index();
            $table->string('active_ingredient', 150)->nullable()->index();
            $table->string('dosage_form', 50)->default('Viên nén');
            $table->string('unit', 30)->default('Viên');
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(20);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->string('dosage_default', 100)->nullable();
            $table->string('frequency_default', 100)->nullable();
            $table->unsignedInteger('duration_default')->default(5);
            $table->string('instructions_default', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};

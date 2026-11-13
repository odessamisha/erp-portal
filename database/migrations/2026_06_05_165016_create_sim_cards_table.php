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
        Schema::create('sim_cards', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('phone_number')->unique();
            $table->foreignId('sim_supplier_id')->constrained('sim_suppliers');
            $table->string('imei')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_cards');
    }
};

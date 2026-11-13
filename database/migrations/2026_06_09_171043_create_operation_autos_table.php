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
        Schema::create('operation_autos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->cascadeOnDelete();
            $table->unsignedInteger('auto_id');
            $table->string('auto_name');
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_autos');
    }
};

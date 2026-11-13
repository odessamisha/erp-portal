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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('serial_number')->nullable();
            $table->date('buy_date')->default(now());

            $table->unsignedBigInteger('tracker_supplier_id');
            $table->foreign('tracker_supplier_id')->references('id')->on('sim_suppliers');

            $table->enum('status',['free','installed','remont'])->default('free');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackers');
    }
};

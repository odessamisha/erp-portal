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
        Schema::create('autos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->isNotEmpty();
            $table->string('imei')->isNotEmpty();
            $table->string('sim_number')->nullable();
            $table->string('sim_serial_number')->nullable();
            $table->foreignId('tracker_supplier_id')->constrained('tracker_suppliers');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('server_id')->constrained('servers');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->date('installation_date')->default(now());
            $table->string('last_position')->nullable();
            $table->dateTime('last_sync')->nullable();
            $table->foreignId('tarif_id')->constrained('tarifs');
            $table->foreignId('sleep_tarif_id')->constrained('tarifs');
            $table->text('notes')->nullable();
            $table->enum('status',['active','sleep','deleted'])->default('active');
            $table->timestamp('delete_date')->nullable();
            $table->timestamp('sleep_date')->nullable();
            $table->timestamps();
            $table->unique(array('imei','server_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autos');
    }
};

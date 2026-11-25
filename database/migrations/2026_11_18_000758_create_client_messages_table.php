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
        Schema::create('client_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('messenger',['viber','telegram','whatsapp','other'])->nullable();
            $table->string('message_id')->nullable();
            $table->string('message');
            $table->enum('status',['prepared','sending','delivered','read','error'])->nullable();
            $table->enum('type',['incoming_message','outgoing_message'])->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_messages');
    }
};

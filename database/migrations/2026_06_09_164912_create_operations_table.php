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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('operation_flow', ['payroll','incoming']);
            $table->foreignId('operation_type_id')->constrained('operation_types')->onDelete('cascade');;
            $table->text('operation_code')->uniqe();
            $table->text('operation_ref')->nullable();
            $table->timestamp('operation_ts');
            $table->unsignedBigInteger('period_year')->nullable();
            $table->enum('period_month',['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'])->nullable();
            $table->text('note')->nullable();
            $table->string('attachment')->nullable();
            $table->BigInteger('total')->default(0);
            $table->BigInteger('transaction_total')->default(0);
            $table->foreignId('linked_operation_id')->nullable()->constrained('operations');
            $table->enum('status',['pending','approved'])->default('pending');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};

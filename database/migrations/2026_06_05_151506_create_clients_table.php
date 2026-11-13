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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->enum('client_type', ['private', 'business'])->default('private');
            $table->enum('payroll_type',['prepay','postpay'])->default('prepay');
            $table->foreignId('server_id')->constrained('servers')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('managers');
            $table->foreignId('city_id')->constrained('cities');
            $table->string('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contract_number')->nullable();
            $table->date('contract_date')->nullable();
            $table->foreignId('exchange_id')->constrained('exchanges');
            $table->string('exchange_data')->nullable();
            $table->string('buhgalter_name')->nullable();
            $table->string('buhgalter_phone')->nullable();
            $table->string('buhgalter_email')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('delivery_address')->nullable();
            $table->bigInteger('balance')->default(0);
            $table->string('access_token')->nullable();
            $table->text('other')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

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
        Schema::table('autos', function (Blueprint $table) {
            //
            $table->foreignId('installer_id')->after('installation_date')->nullable()->constrained('installers');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autos', function (Blueprint $table) {
            //
            $table->dropForeign('autos_installer_id_foreign');
            $table->dropColumn('installer_id');
        });
    }
};

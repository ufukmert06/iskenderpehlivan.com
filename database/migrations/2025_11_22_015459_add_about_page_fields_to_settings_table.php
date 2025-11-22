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
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('happy_customers')->default(0)->after('maintenance_mode');
            $table->integer('therapy_sessions')->default(0)->after('happy_customers');
            $table->integer('certifications_awards')->default(0)->after('therapy_sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['happy_customers', 'therapy_sessions', 'certifications_awards']);
        });
    }
};

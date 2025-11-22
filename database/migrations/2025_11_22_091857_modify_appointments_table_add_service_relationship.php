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
        Schema::table('appointments', function (Blueprint $table) {
            // Drop therapist column
            $table->dropColumn('therapist');

            // Rename service to service_id and add foreign key
            $table->renameColumn('service', 'service_id_temp');
        });

        Schema::table('appointments', function (Blueprint $table) {
            // Add new service_id as foreign key
            $table->foreignId('service_id')->nullable()->after('phone')->constrained('posts')->nullOnDelete();

            // Drop temporary column
            $table->dropColumn('service_id_temp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop foreign key and service_id
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');

            // Restore service as varchar
            $table->string('service')->nullable()->after('phone');

            // Restore therapist column
            $table->string('therapist')->nullable()->after('service');
        });
    }
};

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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('service')->nullable()->after('email')->comment('Hizmet Türü');
            $table->string('ip_address')->nullable()->after('status')->comment('IP Adresi');
            $table->text('user_agent')->nullable()->after('ip_address')->comment('Tarayıcı Bilgisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['service', 'ip_address', 'user_agent']);
        });
    }
};

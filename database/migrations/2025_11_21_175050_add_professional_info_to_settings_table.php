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
            $table->string('whatsapp')->nullable()->comment('WhatsApp iletisim numarasi');
            $table->string('rcc_number')->nullable()->comment('Registered Clinical Counsellor numarasi');
            $table->string('professional_title')->nullable()->comment('Profesyonel unvan');
            $table->integer('years_of_experience')->nullable()->comment('Deneyim yili');
            $table->string('rating')->nullable()->comment('Hizmet puanlamasi');
            $table->text('credentials')->nullable()->comment('Profesyonel sertifika ve kalifikasyonlar');
            $table->text('therapeutic_approach')->nullable()->comment('Terapi yaklas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp',
                'rcc_number',
                'professional_title',
                'years_of_experience',
                'rating',
                'credentials',
                'therapeutic_approach',
            ]);
        });
    }
};

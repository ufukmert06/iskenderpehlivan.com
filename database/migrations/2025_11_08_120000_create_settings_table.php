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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable()->comment('Site logosu yolu');
            $table->string('favicon')->nullable()->comment('Site favicon yolu');
            $table->string('contact_email')->nullable()->comment('Iletisim e-posta adresi');
            $table->string('contact_phone')->nullable()->comment('Iletisim telefon numarasi');
            $table->text('contact_address')->nullable()->comment('Iletisim adresi');
            $table->string('facebook')->nullable()->comment('Facebook profil linki');
            $table->string('twitter')->nullable()->comment('Twitter profil linki');
            $table->string('instagram')->nullable()->comment('Instagram profil linki');
            $table->string('linkedin')->nullable()->comment('LinkedIn profil linki');
            $table->string('youtube')->nullable()->comment('YouTube kanal linki');
            $table->boolean('maintenance_mode')->default(false)->comment('Bakim modu durumu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

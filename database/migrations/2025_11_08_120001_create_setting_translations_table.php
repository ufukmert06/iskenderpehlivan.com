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
        Schema::create('setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained()->onDelete('cascade')->comment('Ana ayar ID');
            $table->string('locale', 2)->comment('Dil kodu (tr, en)');
            $table->string('site_name')->comment('Site adi');
            $table->text('site_description')->nullable()->comment('Site aciklamasi');
            $table->text('footer_text')->nullable()->comment('Footer metni');
            $table->timestamps();

            $table->unique(['setting_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_translations');
    }
};

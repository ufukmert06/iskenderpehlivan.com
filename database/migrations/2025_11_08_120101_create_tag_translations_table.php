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
        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->onDelete('cascade')->comment('Ana etiket ID');
            $table->string('locale', 2)->comment('Dil kodu (tr, en)');
            $table->string('name')->comment('Etiket adi');
            $table->string('slug')->comment('Dile ozel URL slug');
            $table->text('description')->nullable()->comment('Etiket aciklamasi');
            $table->timestamps();

            $table->unique(['tag_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
    }
};

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
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade')->comment('Ana kategori ID');
            $table->string('locale', 2)->comment('Dil kodu');
            $table->string('name')->comment('Kategori adi');
            $table->text('description')->nullable()->comment('Kategori aciklamasi');
            $table->string('slug')->comment('Dile ozel kategori slug');
            $table->timestamps();

            $table->unique(['category_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};

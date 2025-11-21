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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['post', 'category'])->comment('Etiket tipi: yazı veya kategori');
            $table->string('slug_base')->unique()->comment('Temel slug (dil bagimsiz)');
            $table->string('color')->nullable()->comment('Etiket rengi (hex kod)');
            $table->integer('sort_order')->default(0)->comment('Siralama duzeni');
            $table->timestamps();

            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};

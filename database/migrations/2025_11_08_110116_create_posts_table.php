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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['blog', 'page'])->comment('Icerik tipi: blog yazisi veya sayfa');
            $table->string('slug_base')->unique()->comment('Temel slug (dil bagimsiz)');
            $table->enum('status', ['draft', 'published', 'archived'])->comment('Yayin durumu');
            $table->string('featured_image')->nullable()->comment('One cikan gorsel yolu');
            $table->unsignedBigInteger('user_id')->comment('Icerigi olusturan kullanici');
            $table->integer('sort_order')->default(0)->comment('Siralama duzeni');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

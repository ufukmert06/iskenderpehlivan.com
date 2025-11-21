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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['blog', 'page'])->comment('Kategori tipi: blog veya sayfa kategorisi');
            $table->string('slug_base')->comment('Temel kategori slug');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Ust kategori ID (nested icin)');
            $table->integer('sort_order')->default(0)->comment('Kategori siralama duzeni');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['type', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

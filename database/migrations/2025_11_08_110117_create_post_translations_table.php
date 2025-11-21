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
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade')->comment('Ana icerik ID');
            $table->string('locale', 2)->comment('Dil kodu (tr, en)');
            $table->string('title')->comment('Icerik basligi');
            $table->longText('content')->comment('Ana icerik metni');
            $table->text('excerpt')->nullable()->comment('Kisa aciklama/ozet');
            $table->string('meta_title')->nullable()->comment('SEO baslik');
            $table->text('meta_description')->nullable()->comment('SEO aciklama');
            $table->text('meta_keywords')->nullable()->comment('SEO anahtar kelimeler');
            $table->string('og_image')->nullable()->comment('Open Graph gorsel yolu');
            $table->string('og_title')->nullable()->comment('Open Graph baslik');
            $table->text('og_description')->nullable()->comment('Open Graph aciklama');
            $table->string('robots')->nullable()->comment('Robots meta tag (noindex, nofollow vb.)');
            $table->string('canonical_url')->nullable()->comment('Canonical URL (duplicate content icin)');
            $table->string('slug')->unique()->comment('Dile ozel URL slug');
            $table->timestamp('published_at')->nullable()->comment('Yayinlanma tarihi');
            $table->timestamps();

            $table->unique(['post_id', 'locale']);
            $table->index(['locale', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_translations');
    }
};

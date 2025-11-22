<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('slug_base')->comment('Icon for the post (e.g., Font Awesome class)');
        });

        // Update type enum to include 'service'
        DB::statement('UPDATE posts SET type = type');
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('type', ['blog', 'page', 'service'])->comment('Content type: blog post, page, or service')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove service type posts before changing enum
        DB::statement("DELETE FROM posts WHERE type = 'service'");

        Schema::table('posts', function (Blueprint $table) {
            $table->enum('type', ['blog', 'page'])->comment('Content type: blog post or page')->change();
            $table->dropColumn('icon');
        });
    }
};

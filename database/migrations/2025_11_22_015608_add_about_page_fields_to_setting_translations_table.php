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
        Schema::table('setting_translations', function (Blueprint $table) {
            $table->string('about_welcome_title')->nullable()->after('footer_text');
            $table->text('about_welcome_description')->nullable()->after('about_welcome_title');
            $table->string('about_mission_title')->nullable()->after('about_welcome_description');
            $table->text('about_mission_content')->nullable()->after('about_mission_title');
            $table->string('about_vision_title')->nullable()->after('about_mission_content');
            $table->text('about_vision_content')->nullable()->after('about_vision_title');
            $table->string('counter_years_label')->nullable()->after('about_vision_content');
            $table->string('counter_customers_label')->nullable()->after('counter_years_label');
            $table->string('counter_sessions_label')->nullable()->after('counter_customers_label');
            $table->string('counter_certifications_label')->nullable()->after('counter_sessions_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_translations', function (Blueprint $table) {
            $table->dropColumn([
                'about_welcome_title',
                'about_welcome_description',
                'about_mission_title',
                'about_mission_content',
                'about_vision_title',
                'about_vision_content',
                'counter_years_label',
                'counter_customers_label',
                'counter_sessions_label',
                'counter_certifications_label',
            ]);
        });
    }
};

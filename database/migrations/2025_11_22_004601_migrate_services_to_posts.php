<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first user ID for authorship
        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if (! $firstUserId) {
            // If no users exist, we can't migrate (posts require user_id)
            return;
        }

        // Get all services
        $services = DB::table('services')->get();

        foreach ($services as $service) {
            // Create post from service
            $postId = DB::table('posts')->insertGetId([
                'type' => 'service',
                'slug_base' => $service->slug_base,
                'status' => 'published', // Services don't have status, default to published
                'featured_image' => $service->featured_image,
                'icon' => $service->icon,
                'user_id' => $firstUserId,
                'sort_order' => $service->sort_order,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ]);

            // Migrate service translations to post translations
            $serviceTranslations = DB::table('service_translations')
                ->where('service_id', $service->id)
                ->get();

            foreach ($serviceTranslations as $translation) {
                DB::table('post_translations')->insert([
                    'post_id' => $postId,
                    'locale' => $translation->locale,
                    'title' => $translation->name, // Map 'name' to 'title'
                    'slug' => $translation->slug,
                    'content' => $translation->description ?? '', // Map 'description' to 'content'
                    'excerpt' => null,
                    'meta_title' => null,
                    'meta_description' => null,
                    'meta_keywords' => null,
                    'og_image' => null,
                    'og_title' => null,
                    'og_description' => null,
                    'robots' => null,
                    'canonical_url' => null,
                    'published_at' => now(),
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all service-type posts
        $servicePosts = DB::table('posts')->where('type', 'service')->get();

        foreach ($servicePosts as $post) {
            // Restore service
            $serviceId = DB::table('services')->insertGetId([
                'slug_base' => $post->slug_base,
                'icon' => $post->icon,
                'featured_image' => $post->featured_image,
                'sort_order' => $post->sort_order,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ]);

            // Restore service translations
            $postTranslations = DB::table('post_translations')
                ->where('post_id', $post->id)
                ->get();

            foreach ($postTranslations as $translation) {
                DB::table('service_translations')->insert([
                    'service_id' => $serviceId,
                    'locale' => $translation->locale,
                    'name' => $translation->title, // Map 'title' back to 'name'
                    'slug' => $translation->slug,
                    'description' => $translation->content, // Map 'content' back to 'description'
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                ]);
            }

            // Delete the post translations
            DB::table('post_translations')->where('post_id', $post->id)->delete();

            // Delete the post
            DB::table('posts')->where('id', $post->id)->delete();
        }
    }
};

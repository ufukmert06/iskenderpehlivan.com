<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TagCollection;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use App\Traits\CachesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Tags
 */
class TagController extends Controller
{
    use CachesApiResponses;

    /**
     * Get all tags (paginated)
     *
     * Returns a paginated list of all tags with post counts.
     *
     * @group Tags
     *
     * @queryParam type string Filter by tag type. Example: topic
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam per_page integer Number of items per page. Default: 20. Example: 15
     * @queryParam page integer Page number. Default: 1. Example: 1
     * @queryParam with_posts boolean Include posts in response. Default: false. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "type": "topic",
     *       "slug": "laravel",
     *       "color": "#FF2D20",
     *       "sort_order": 0,
     *       "translation": {
     *         "locale": "tr",
     *         "name": "Laravel",
     *         "slug": "laravel"
     *       },
     *       "posts_count": 25,
     *       "created_at": "2025-11-08T10:00:00.000000Z",
     *       "updated_at": "2025-11-08T10:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 50,
     *     "per_page": 20,
     *     "current_page": 1,
     *     "last_page": 3,
     *     "from": 1,
     *     "to": 20
     *   }
     * }
     */
    public function index(Request $request): TagCollection
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('tags');

        $cacheKey = $this->getCacheKey("api_v{$version}_tags_list", [
            'locale' => $locale,
            'type' => $request->query('type'),
            'with_posts' => $request->boolean('with_posts') ? '1' : '0',
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request) {
            $query = Tag::query()
                ->with(['translations'])
                ->withCount('posts');

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->boolean('with_posts')) {
                $query->with(['posts.translations', 'posts.user']);
            }

            $query->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc');

            $perPage = $request->query('per_page', 20);
            $tags = $query->paginate(min($perPage, 100));

            return new TagCollection($tags);
        });
    }

    /**
     * Get a single tag by slug
     *
     * Returns detailed information about a specific tag including its posts.
     *
     * @group Tags
     *
     * @urlParam slug string required The slug of the tag. Example: laravel
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam per_page integer Number of posts per page. Default: 20. Example: 15
     * @queryParam page integer Page number for posts. Default: 1. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "type": "topic",
     *     "slug": "laravel",
     *     "color": "#FF2D20",
     *     "sort_order": 0,
     *     "translation": {
     *       "locale": "tr",
     *       "name": "Laravel",
     *       "slug": "laravel"
     *     },
     *     "posts_count": 25,
     *     "posts": [
     *       {
     *         "id": 1,
     *         "type": "blog",
     *         "slug": "my-first-post",
     *         "translation": {
     *           "locale": "tr",
     *           "title": "İlk Yazım"
     *         }
     *       }
     *     ],
     *     "created_at": "2025-11-08T10:00:00.000000Z",
     *     "updated_at": "2025-11-08T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tag not found"
     * }
     */
    public function show(Request $request, string $slug): TagResource|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('tags');

        $cacheKey = $this->getCacheKey("api_v{$version}_tags_show", [
            'slug' => $slug,
            'locale' => $locale,
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request, $slug, $locale) {
            // Try to find by translation slug first
            $tag = Tag::with(['translations'])
                ->withCount('posts')
                ->whereHas('translations', function ($query) use ($slug, $locale) {
                    $query->where('slug', $slug)->where('locale', $locale);
                })
                ->first();

            // If not found, try slug_base
            if (! $tag) {
                $tag = Tag::with(['translations'])
                    ->withCount('posts')
                    ->where('slug_base', $slug)
                    ->first();
            }

            if (! $tag) {
                return response()->json(['message' => 'Tag not found'], 404);
            }

            // Load posts with pagination
            $perPage = $request->query('per_page', 20);
            $tag->load([
                'posts' => function ($query) use ($perPage) {
                    $query->with(['translations', 'user'])
                        ->orderBy('sort_order', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->limit($perPage);
                },
            ]);

            return new TagResource($tag);
        });
    }

    /**
     * Get all posts with a specific tag (paginated)
     *
     * Returns a paginated list of all posts that have a specific tag.
     * This is the recommended endpoint for listing tag posts with full pagination support.
     *
     * @group Tags
     *
     * @urlParam slug string required The slug of the tag. Example: laravel
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam type string Filter by post type (blog or page). Example: blog
     * @queryParam status string Filter by status (draft, published, etc.). Example: published
     * @queryParam per_page integer Number of items per page. Default: 20. Example: 15
     * @queryParam page integer Page number. Default: 1. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "type": "blog",
     *       "slug": "my-first-post",
     *       "status": "published",
     *       "featured_image": "http://localhost/storage/posts/image.jpg",
     *       "sort_order": 0,
     *       "author": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "john@example.com"
     *       },
     *       "translation": {
     *         "locale": "tr",
     *         "title": "İlk Yazım",
     *         "slug": "ilk-yazim",
     *         "content": "<p>Content here...</p>",
     *         "excerpt": "Short excerpt",
     *         "meta_title": "SEO Title",
     *         "meta_description": "SEO Description",
     *         "meta_keywords": "seo, keywords"
     *       },
     *       "categories": [],
     *       "tags": [],
     *       "created_at": "2025-11-08T10:00:00.000000Z",
     *       "updated_at": "2025-11-08T10:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 25,
     *     "per_page": 20,
     *     "current_page": 1,
     *     "last_page": 2,
     *     "from": 1,
     *     "to": 20
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Tag not found"
     * }
     */
    public function posts(Request $request, string $slug): \App\Http\Resources\Api\PostCollection|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('tags');

        $cacheKey = $this->getCacheKey("api_v{$version}_tag_posts", [
            'slug' => $slug,
            'locale' => $locale,
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request, $slug, $locale) {
            // Find tag by slug
            $tag = Tag::with(['translations'])
                ->whereHas('translations', function ($query) use ($slug, $locale) {
                    $query->where('slug', $slug)->where('locale', $locale);
                })
                ->first();

            // If not found, try slug_base
            if (! $tag) {
                $tag = Tag::where('slug_base', $slug)->first();
            }

            if (! $tag) {
                return response()->json(['message' => 'Tag not found'], 404);
            }

            // Get posts for this tag
            $query = $tag->posts()
                ->with(['user', 'translations', 'categories.translations', 'tags.translations']);

            // Filter by type (blog or page)
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Order by sort_order and created_at
            $query->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc');

            $perPage = $request->query('per_page', 20);
            $posts = $query->paginate(min($perPage, 100));

            return new \App\Http\Resources\Api\PostCollection($posts);
        });
    }
}

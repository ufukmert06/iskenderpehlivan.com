<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PostCollection;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use App\Traits\CachesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Posts
 */
class PostController extends Controller
{
    use CachesApiResponses;
    /**
     * Get all posts (paginated)
     *
     * Returns a paginated list of all posts (blogs and pages).
     * You can filter by type, status, and locale.
     *
     * @group Posts
     *
     * @queryParam type string Filter by post type (blog or page). Example: blog
     * @queryParam status string Filter by status (draft, published, etc.). Example: published
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam per_page integer Number of items per page. Default: 20. Example: 15
     * @queryParam page integer Page number. Default: 1. Example: 1
     * @queryParam category string Filter by category slug. Example: technology
     * @queryParam tag string Filter by tag slug. Example: laravel
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
     *     "total": 100,
     *     "per_page": 20,
     *     "current_page": 1,
     *     "last_page": 5,
     *     "from": 1,
     *     "to": 20
     *   }
     * }
     */
    public function index(Request $request): PostCollection
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('posts');

        $cacheKey = $this->getCacheKey("api_v{$version}_posts_list", [
            'locale' => $locale,
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'category' => $request->query('category'),
            'tag' => $request->query('tag'),
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request) {
            $query = Post::query()
                ->with(['user', 'translations', 'categories.translations', 'tags.translations']);

            // Filter by type (blog or page)
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('category')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug_base', $request->category)
                        ->orWhereHas('translations', function ($tq) use ($request) {
                            $tq->where('slug', $request->category);
                        });
                });
            }

            // Filter by tag
            if ($request->has('tag')) {
                $query->whereHas('tags', function ($q) use ($request) {
                    $q->where('slug_base', $request->tag)
                        ->orWhereHas('translations', function ($tq) use ($request) {
                            $tq->where('slug', $request->tag);
                        });
                });
            }

            // Order by sort_order and created_at
            $query->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc');

            $perPage = $request->query('per_page', 20);
            $posts = $query->paginate(min($perPage, 100));

            return new PostCollection($posts);
        });
    }

    /**
     * Get a single post by slug
     *
     * Returns detailed information about a specific post including all relationships.
     *
     * @group Posts
     *
     * @urlParam slug string required The slug of the post. Example: my-first-post
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "type": "blog",
     *     "slug": "my-first-post",
     *     "status": "published",
     *     "featured_image": "http://localhost/storage/posts/image.jpg",
     *     "sort_order": 0,
     *     "author": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com"
     *     },
     *     "translation": {
     *       "locale": "tr",
     *       "title": "İlk Yazım",
     *       "slug": "ilk-yazim",
     *       "content": "<p>Full content here...</p>",
     *       "excerpt": "Short excerpt",
     *       "meta_title": "SEO Title",
     *       "meta_description": "SEO Description",
     *       "meta_keywords": "seo, keywords"
     *     },
     *     "categories": [
     *       {
     *         "id": 1,
     *         "slug": "technology",
     *         "translation": {
     *           "locale": "tr",
     *           "name": "Teknoloji",
     *           "slug": "teknoloji"
     *         }
     *       }
     *     ],
     *     "tags": [
     *       {
     *         "id": 1,
     *         "slug": "laravel",
     *         "color": "#FF2D20",
     *         "translation": {
     *           "locale": "tr",
     *           "name": "Laravel",
     *           "slug": "laravel"
     *         }
     *       }
     *     ],
     *     "created_at": "2025-11-08T10:00:00.000000Z",
     *     "updated_at": "2025-11-08T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Post not found"
     * }
     */
    public function show(Request $request, string $slug): PostResource|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('posts');

        $cacheKey = $this->getCacheKey("api_v{$version}_posts_show", [
            'slug' => $slug,
            'locale' => $locale,
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request, $slug, $locale) {
            // Try to find by translation slug first
            $post = Post::with(['user', 'translations', 'categories.translations', 'tags.translations'])
                ->whereHas('translations', function ($query) use ($slug, $locale) {
                    $query->where('slug', $slug)->where('locale', $locale);
                })
                ->first();

            // If not found, try slug_base
            if (! $post) {
                $post = Post::with(['user', 'translations', 'categories.translations', 'tags.translations'])
                    ->where('slug_base', $slug)
                    ->first();
            }

            if (! $post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            return new PostResource($post);
        });
    }
}

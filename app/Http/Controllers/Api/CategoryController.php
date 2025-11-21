<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryCollection;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Traits\CachesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Categories
 */
class CategoryController extends Controller
{
    use CachesApiResponses;
    /**
     * Get all categories (paginated)
     *
     * Returns a paginated list of all categories with post counts.
     *
     * @group Categories
     *
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam per_page integer Number of items per page. Default: 20. Example: 15
     * @queryParam page integer Page number. Default: 1. Example: 1
     * @queryParam with_posts boolean Include posts in response. Default: false. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "slug": "technology",
     *       "sort_order": 0,
     *       "translation": {
     *         "locale": "tr",
     *         "name": "Teknoloji",
     *         "slug": "teknoloji",
     *         "description": "Teknoloji ile ilgili yazılar"
     *       },
     *       "posts_count": 15,
     *       "created_at": "2025-11-08T10:00:00.000000Z",
     *       "updated_at": "2025-11-08T10:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "total": 10,
     *     "per_page": 20,
     *     "current_page": 1,
     *     "last_page": 1,
     *     "from": 1,
     *     "to": 10
     *   }
     * }
     */
    public function index(Request $request): CategoryCollection
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('categories');

        $cacheKey = $this->getCacheKey("api_v{$version}_categories_list", [
            'locale' => $locale,
            'with_posts' => $request->boolean('with_posts') ? '1' : '0',
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request) {
            $query = Category::query()
                ->with(['translations'])
                ->withCount('posts');

            if ($request->boolean('with_posts')) {
                $query->with(['posts.translations', 'posts.user']);
            }

            $query->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc');

            $perPage = $request->query('per_page', 20);
            $categories = $query->paginate(min($perPage, 100));

            return new CategoryCollection($categories);
        });
    }

    /**
     * Get a single category by slug
     *
     * Returns detailed information about a specific category including its posts.
     *
     * @group Categories
     *
     * @urlParam slug string required The slug of the category. Example: technology
     * @queryParam locale string Language code for translations (tr, en). Default: tr. Example: tr
     * @queryParam per_page integer Number of posts per page. Default: 20. Example: 15
     * @queryParam page integer Page number for posts. Default: 1. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "slug": "technology",
     *     "sort_order": 0,
     *     "translation": {
     *       "locale": "tr",
     *       "name": "Teknoloji",
     *       "slug": "teknoloji",
     *       "description": "Teknoloji ile ilgili yazılar"
     *     },
     *     "posts_count": 15,
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
     *   "message": "Category not found"
     * }
     */
    public function show(Request $request, string $slug): CategoryResource|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('categories');

        $cacheKey = $this->getCacheKey("api_v{$version}_categories_show", [
            'slug' => $slug,
            'locale' => $locale,
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request, $slug, $locale) {
            // Try to find by translation slug first
            $category = Category::with(['translations'])
                ->withCount('posts')
                ->whereHas('translations', function ($query) use ($slug, $locale) {
                    $query->where('slug', $slug)->where('locale', $locale);
                })
                ->first();

            // If not found, try slug_base
            if (! $category) {
                $category = Category::with(['translations'])
                    ->withCount('posts')
                    ->where('slug_base', $slug)
                    ->first();
            }

            if (! $category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            // Load posts with pagination
            $perPage = $request->query('per_page', 20);
            $category->load([
                'posts' => function ($query) use ($perPage) {
                    $query->with(['translations', 'user'])
                        ->orderBy('sort_order', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->limit($perPage);
                },
            ]);

            return new CategoryResource($category);
        });
    }

    /**
     * Get all posts in a specific category (paginated)
     *
     * Returns a paginated list of all posts that belong to a specific category.
     * This is the recommended endpoint for listing category posts with full pagination support.
     *
     * @group Categories
     *
     * @urlParam slug string required The slug of the category. Example: technology
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
     *     "total": 50,
     *     "per_page": 20,
     *     "current_page": 1,
     *     "last_page": 3,
     *     "from": 1,
     *     "to": 20
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Category not found"
     * }
     */
    public function posts(Request $request, string $slug): \App\Http\Resources\Api\PostCollection|Response
    {
        $locale = $request->query('locale', $request->header('Accept-Language', config('app.locale')));
        $version = $this->getCacheVersion('categories');

        $cacheKey = $this->getCacheKey("api_v{$version}_category_posts", [
            'slug' => $slug,
            'locale' => $locale,
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ]);

        return $this->cacheResponse($cacheKey, function () use ($request, $slug, $locale) {
            // Find category by slug
            $category = Category::with(['translations'])
                ->whereHas('translations', function ($query) use ($slug, $locale) {
                    $query->where('slug', $slug)->where('locale', $locale);
                })
                ->first();

            // If not found, try slug_base
            if (! $category) {
                $category = Category::where('slug_base', $slug)->first();
            }

            if (! $category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            // Get posts for this category
            $query = $category->posts()
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

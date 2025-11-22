# Optional API Architecture

This document describes the optional readonly public API for external integrations.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [API Endpoints](#api-endpoints)
2. [API Features](#api-features)
3. [API Components](#api-components)
4. [CORS Configuration](#cors-configuration)
5. [API Documentation](#api-documentation)
6. [Next.js Integration Examples](#nextjs-integration-examples)

---

## API Endpoints

While this is primarily a traditional CMS with a public frontend, it also provides an optional **readonly public API** for external integrations or headless usage.

### Posts (`/api/posts`)

- `GET /api/posts` - List all posts (blogs and pages)
  - Query params: `type` (blog/page), `status`, `locale`, `category`, `tag`, `per_page`, `page`
- `GET /api/posts/{slug}` - Get single post by slug
  - Query params: `locale`

### Categories (`/api/categories`)

- `GET /api/categories` - List all categories
  - Query params: `locale`, `per_page`, `page`, `with_posts`
- `GET /api/categories/{slug}` - Get single category with posts
  - Query params: `locale`, `per_page`, `page`
- `GET /api/categories/{slug}/posts` - Get all posts in a category (paginated) ⭐
  - Query params: `locale`, `type`, `status`, `per_page`, `page`

### Tags (`/api/tags`)

- `GET /api/tags` - List all tags
  - Query params: `type`, `locale`, `per_page`, `page`, `with_posts`
- `GET /api/tags/{slug}` - Get single tag with posts
  - Query params: `locale`, `per_page`, `page`
- `GET /api/tags/{slug}/posts` - Get all posts with a tag (paginated) ⭐
  - Query params: `locale`, `type`, `status`, `per_page`, `page`

### Settings (`/api/settings`)

- `GET /api/settings` - Get global site settings
  - Query params: `locale`

### Health Check

- `GET /api/health` - API health check

**[⬆ Back to Top](#table-of-contents)**

---

## API Features

### Multi-Language Support

- Locale can be set via query parameter (`?locale=tr`) or `Accept-Language` header
- Supported locales: `tr` (default), `en`
- Automatic fallback to default locale if translation not found

### Pagination

- Default: 20 items per page
- Maximum: 100 items per page
- Query params: `per_page`, `page`

### Caching (6 Hours)

- All API responses are cached for **6 hours** to reduce database load
- Cache automatically invalidates when content is created, updated, or deleted
- Version-based cache invalidation ensures fresh content after changes
- Cache keys include all query parameters (locale, type, filters, pagination)
- Implementation details:
  - Trait: `CachesApiResponses` ([../app/Traits/CachesApiResponses.php](../app/Traits/CachesApiResponses.php))
  - Observers: `PostObserver`, `CategoryObserver`, `TagObserver`, `SettingObserver` ([../app/Observers/](../app/Observers/))
  - Cache versions increment on model changes, invalidating all related cache entries
  - Manual cache clear: `php artisan cache:clear` or use `CachesApiResponses::clearAllApiCache()`

### Response Structure

```json
{
  "data": [ /* resources */ ],
  "meta": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 20
  }
}
```

**[⬆ Back to Top](#table-of-contents)**

---

## API Components

### Resources

Located in [../app/Http/Resources/Api/](../app/Http/Resources/Api/):

- `PostResource` - Formats post data with translations
- `CategoryResource` - Formats category data with translations
- `TagResource` - Formats tag data with translations
- `SettingResource` - Formats settings data with translations
- `*Collection` classes - Handle paginated collections

### Controllers

Located in [../app/Http/Controllers/Api/](../app/Http/Controllers/Api/):

- `PostController` - Readonly post endpoints
- `CategoryController` - Readonly category endpoints
- `TagController` - Readonly tag endpoints
- `SettingController` - Readonly settings endpoint
- All controllers are readonly (only `index()` and `show()` methods)

### Middleware

- `SetApiLocale` - Handles locale detection and setting

**[⬆ Back to Top](#table-of-contents)**

---

## CORS Configuration

Configured in [../config/cors.php](../config/cors.php) to allow requests from:

- `http://localhost:3000` (Next.js dev)
- `http://localhost:3001`
- Custom frontend URL (via `FRONTEND_URL` env variable)

**[⬆ Back to Top](#table-of-contents)**

---

## API Documentation

**Scramble** - Interactive API documentation available at `/docs/api`

- Configured in [../config/scramble.php](../config/scramble.php)
- Detailed endpoint documentation with examples
- Try-it functionality for testing endpoints
- OpenAPI 3.0 specification
- Includes Next.js usage examples

**[⬆ Back to Top](#table-of-contents)**

---

## Next.js Integration Examples

### Fetching Posts

```typescript
// app/lib/api.ts
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

export async function getPosts(locale: string = 'tr', type?: 'blog' | 'page') {
  const params = new URLSearchParams({ locale });
  if (type) params.append('type', type);

  const res = await fetch(`${API_URL}/posts?${params}`);
  if (!res.ok) throw new Error('Failed to fetch posts');
  return res.json();
}

export async function getPost(slug: string, locale: string = 'tr') {
  const res = await fetch(`${API_URL}/posts/${slug}?locale=${locale}`);
  if (!res.ok) throw new Error('Failed to fetch post');
  return res.json();
}

export async function getCategoryPosts(categorySlug: string, locale: string = 'tr', type?: 'blog' | 'page') {
  const params = new URLSearchParams({ locale });
  if (type) params.append('type', type);

  const res = await fetch(`${API_URL}/categories/${categorySlug}/posts?${params}`);
  if (!res.ok) throw new Error('Failed to fetch category posts');
  return res.json();
}

export async function getTagPosts(tagSlug: string, locale: string = 'tr', type?: 'blog' | 'page') {
  const params = new URLSearchParams({ locale });
  if (type) params.append('type', type);

  const res = await fetch(`${API_URL}/tags/${tagSlug}/posts?${params}`);
  if (!res.ok) throw new Error('Failed to fetch tag posts');
  return res.json();
}
```

### Server Component (Next.js 14+)

```typescript
// app/blog/page.tsx
import { getPosts } from '@/lib/api';

export default async function BlogPage() {
  const { data: posts } = await getPosts('tr', 'blog');

  return (
    <div>
      {posts.map(post => (
        <article key={post.id}>
          <h2>{post.translation.title}</h2>
          <p>{post.translation.excerpt}</p>
        </article>
      ))}
    </div>
  );
}
```

### ISR with Revalidation

```typescript
// app/blog/[slug]/page.tsx
export const revalidate = 3600; // Revalidate every hour

export async function generateStaticParams() {
  const { data: posts } = await getPosts('tr', 'blog');
  return posts.map(post => ({ slug: post.slug }));
}
```

**[⬆ Back to Top](#table-of-contents)**

---

**[← Back to Main Documentation](../CLAUDE.md)**

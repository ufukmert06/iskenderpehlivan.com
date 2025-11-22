# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Table of Contents

1. [Project Overview](#project-overview)
2. [Key Technologies](#key-technologies)
3. [Development Commands](#development-commands)
   - [Initial Setup](#initial-setup)
   - [Development Server](#development-server)
   - [Testing](#testing)
   - [Code Formatting](#code-formatting)
   - [Frontend Build](#frontend-build)
4. [CMS Architecture](#cms-architecture)
   - [Content Models](#content-models)
   - [Filament Resources](#filament-resources)
   - [Filament Pages](#filament-pages)
   - [Dashboard Widgets](#dashboard-widgets)
5. [Public Frontend (Volt)](#public-frontend-volt)
6. [Optional API Architecture](#optional-api-architecture)
   - [API Endpoints](#api-endpoints-public---no-authentication)
   - [API Features](#api-features)
   - [API Components](#api-components)
   - [API Documentation](#api-documentation)
7. [Multi-Language Translation Pattern](#multi-language-translation-pattern)
8. [Project Structure](#project-structure)
9. [Filament Resource Creation](#filament-resource-creation)
10. [Database & Queues](#database--queues)
11. [Testing Conventions](#testing-conventions)
12. [Common Patterns](#common-patterns)
    - [Creating a Complete CRUD Feature](#creating-a-complete-crud-feature)
    - [Creating a Translatable Model](#creating-a-translatable-model)
    - [Creating a Custom Filament Page](#creating-a-custom-filament-page-with-form)
13. [Important Notes](#important-notes)
14. [Troubleshooting](#troubleshooting)

---

## Project Overview

This is a **Content Management System (CMS)** built with Filament 3 on Laravel 12. The system features a public-facing website powered by Livewire Volt and a comprehensive admin panel for content management. Content (posts, pages, services, categories, tags, settings) is fully translatable across multiple locales using a custom translation pattern.

**Architecture:**
- **Backend/Admin**: Filament 3 panel for content management (`/admin`)
- **Public Frontend**: Livewire Volt pages for public-facing website (root path `/`)
- **Multi-Language**: Supports Turkish (default) and English with URL-based locale switching
- **Database**: SQLite (development) - content storage with translation support
- **Optional API**: Public readonly RESTful API available at `/api/*` for external integrations

**[⬆ Back to Top](#table-of-contents)**

---

## Key Technologies

- **Backend:** Laravel 12, PHP 8.2+
- **Admin Panel:** Filament 3.3 with amidesfahani/filament-tinyeditor for rich text editing
- **Frontend:** Vite 7, Tailwind CSS 4
- **Testing:** Pest 4
- **Code Style:** Laravel Pint
- **Database:** SQLite (default), with database-backed sessions, cache, and queue

**[⬆ Back to Top](#table-of-contents)**

---

## Development Commands

### Initial Setup
```bash
composer run setup
```
This command installs dependencies, copies .env.example to .env, generates app key, runs migrations, and builds frontend assets.

### Development Server
```bash
composer run dev
```
Runs three processes concurrently:
- Laravel development server (`php artisan serve`)
- Queue worker (`php artisan queue:listen --tries=1`)
- Vite dev server (`npm run dev`)

Alternatively, run services individually:
```bash
php artisan serve          # Start Laravel server
php artisan queue:listen   # Start queue worker
npm run dev               # Start Vite dev server
```

### Testing
```bash
composer run test          # Run all tests
php artisan test          # Run all tests (alternative)
php artisan test --filter=testName  # Run specific test
php artisan test tests/Feature/ExampleTest.php  # Run specific file
```

### Code Formatting
```bash
vendor/bin/pint --dirty   # Format modified files
vendor/bin/pint          # Format all files
```

### Frontend Build
```bash
npm run build  # Production build
npm run dev    # Development mode with hot reload
```

**[⬆ Back to Top](#table-of-contents)**

---

## CMS Architecture

This application is a multi-language Content Management System with the following structure:

### Content Models

**Posts** - Main content model with type differentiation:
- **Fields:** `type` (blog/page), `status`, `slug_base`, `featured_image`, `user_id`, `sort_order`
- **Relationships:**
  - `user()` - Belongs to User (author)
  - `translations()` - Has many PostTranslation
  - `categories()` - Belongs to many Category
  - `tags()` - Belongs to many Tag
- **Helper:** `translation(?string $locale)` - Get translation for specific locale

**Categories** - For organizing content:
- **Fields:** `slug_base`, `sort_order`
- **Relationships:**
  - `translations()` - Has many CategoryTranslation
  - `posts()` - Belongs to many Post
- **Helper:** `translation(?string $locale)`

**Tags** - For tagging content:
- **Fields:** `type`, `slug_base`, `color`, `sort_order`
- **Relationships:**
  - `translations()` - Has many TagTranslation
  - `posts()` - Belongs to many Post
- **Helper:** `translation(?string $locale)`

**Settings** - Global site settings (Singleton pattern):
- **Fields:** `logo`, `favicon`, contact info, social media links, `maintenance_mode`
- **Relationships:** `translations()` - Has many SettingTranslation
- **Management:** Custom Filament Page (`ManageSettings`) with Repeater for translations
- **Pattern:** Single record in database, managed via custom page, not a resource

### Filament Resources

- **BlogResource**: Manages blog posts (Post model where type='blog')
- **PageResource**: Manages pages (Post model where type='page')
- **PostResource**: General post management
- **CategoryResource**: Category management with translations
- **TagResource**: Tag management with translations and color coding

### Filament Pages

- **ManageSettings**: Custom page for managing global site settings
  - Uses singleton pattern (one Setting record)
  - Repeater field for managing translations
  - Manual save logic for handling translations

### Dashboard Widgets

Located in `app/Filament/Widgets/` (auto-discovered):
- **StatsOverview**: Overview statistics cards
- **PostsByStatusChart**: Chart showing posts by status
- **PostsByTypeChart**: Chart showing posts by type
- **LatestPosts**: Table widget showing recent posts

**[⬆ Back to Top](#table-of-contents)**

---

## Public Frontend (Volt)

The public-facing website is built using **Livewire Volt** (functional components) and supports multi-language routing.

### Routes Structure

Routes are defined in `routes/web.php`:

**English Routes** (Default - no prefix):
- `/` - Home page
- `/contact` - Contact page
- `/services` - Services listing
- `/services/{slug}` - Individual service detail

**Turkish Routes** (`/tr` prefix):
- `/tr/` - Ana sayfa (Home)
- `/tr/iletisim` - İletişim (Contact)
- `/tr/hizmetler` - Hizmetler (Services)
- `/tr/hizmetler/{slug}` - Hizmet detayı (Service detail)

### Volt Components

Volt components are located in `resources/views/livewire/`:
- `home.blade.php` - Homepage with hero, about, services, blog, and contact sections
- `contact.blade.php` - Contact page
- `services.blade.php` - Services listing page
- `service.blade.php` - Individual service detail page

### Volt Component Pattern

All public Volt components use the **functional** style:

```php
@volt
<?php
// Fetch data directly (non-reactive)
$posts = \App\Models\Post::where('type', 'blog')
    ->where('status', 'published')
    ->with(['translations', 'categories.translations'])
    ->latest()
    ->limit(3)
    ->get();
?>

<div>
    <!-- Blade template here -->
</div>
@endvolt
```

### Language Switching

- Language is determined by URL prefix (`/tr/*` for Turkish, no prefix for English)
- Translation strings use Laravel's `__()` helper (e.g., `__('home.hero.title')`)
- Translation files located in `lang/en/` and `lang/tr/`
- Content models use `translation(?string $locale)` helper to get localized content

**[⬆ Back to Top](#table-of-contents)**

---

## Optional API Architecture

While this is primarily a traditional CMS with a public frontend, it also provides an optional **readonly public API** for external integrations or headless usage.

### API Endpoints (Public - No Authentication)

**Posts** (`/api/posts`)
- `GET /api/posts` - List all posts (blogs and pages)
  - Query params: `type` (blog/page), `status`, `locale`, `category`, `tag`, `per_page`, `page`
- `GET /api/posts/{slug}` - Get single post by slug
  - Query params: `locale`

**Categories** (`/api/categories`)
- `GET /api/categories` - List all categories
  - Query params: `locale`, `per_page`, `page`, `with_posts`
- `GET /api/categories/{slug}` - Get single category with posts
  - Query params: `locale`, `per_page`, `page`
- `GET /api/categories/{slug}/posts` - Get all posts in a category (paginated) ⭐
  - Query params: `locale`, `type`, `status`, `per_page`, `page`

**Tags** (`/api/tags`)
- `GET /api/tags` - List all tags
  - Query params: `type`, `locale`, `per_page`, `page`, `with_posts`
- `GET /api/tags/{slug}` - Get single tag with posts
  - Query params: `locale`, `per_page`, `page`
- `GET /api/tags/{slug}/posts` - Get all posts with a tag (paginated) ⭐
  - Query params: `locale`, `type`, `status`, `per_page`, `page`

**Settings** (`/api/settings`)
- `GET /api/settings` - Get global site settings
  - Query params: `locale`

**Health Check**
- `GET /api/health` - API health check

### API Features

**Multi-Language Support:**
- Locale can be set via query parameter (`?locale=tr`) or `Accept-Language` header
- Supported locales: `tr` (default), `en`
- Automatic fallback to default locale if translation not found

**Pagination:**
- Default: 20 items per page
- Maximum: 100 items per page
- Query params: `per_page`, `page`

**Caching (6 Hours):**
- All API responses are cached for **6 hours** to reduce database load
- Cache automatically invalidates when content is created, updated, or deleted
- Version-based cache invalidation ensures fresh content after changes
- Cache keys include all query parameters (locale, type, filters, pagination)
- Implementation details:
  - Trait: `CachesApiResponses` ([app/Traits/CachesApiResponses.php](app/Traits/CachesApiResponses.php))
  - Observers: `PostObserver`, `CategoryObserver`, `TagObserver`, `SettingObserver` ([app/Observers/](app/Observers/))
  - Cache versions increment on model changes, invalidating all related cache entries
  - Manual cache clear: `php artisan cache:clear` or use `CachesApiResponses::clearAllApiCache()`

**Response Structure:**
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

### API Components

**Resources** ([app/Http/Resources/Api/](app/Http/Resources/Api/))
- `PostResource` - Formats post data with translations
- `CategoryResource` - Formats category data with translations
- `TagResource` - Formats tag data with translations
- `SettingResource` - Formats settings data with translations
- `*Collection` classes - Handle paginated collections

**Controllers** ([app/Http/Controllers/Api/](app/Http/Controllers/Api/))
- `PostController` - Readonly post endpoints
- `CategoryController` - Readonly category endpoints
- `TagController` - Readonly tag endpoints
- `SettingController` - Readonly settings endpoint
- All controllers are readonly (only `index()` and `show()` methods)

**Middleware:**
- `SetApiLocale` - Handles locale detection and setting

### CORS Configuration

Configured in [config/cors.php](config/cors.php) to allow requests from:
- `http://localhost:3000` (Next.js dev)
- `http://localhost:3001`
- Custom frontend URL (via `FRONTEND_URL` env variable)

### API Documentation

**Scramble** - Interactive API documentation available at `/docs/api`
- Configured in [config/scramble.php](config/scramble.php)
- Detailed endpoint documentation with examples
- Try-it functionality for testing endpoints
- OpenAPI 3.0 specification
- Includes Next.js usage examples

### Next.js Integration Examples

**Fetching Posts:**
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

**Server Component (Next.js 14+):**
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

**ISR with Revalidation:**
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

## Multi-Language Translation Pattern

All content models use a custom translation pattern:

### Structure

- **Main Model**: Stores language-independent data (IDs, slugs, images, foreign keys, etc.)
- **Translation Model**: Stores language-specific data with `locale` field (e.g., title, content, meta_description)
- **Relationship**: `translations()` HasMany relationship on main model
- **Helper Method**: `translation(?string $locale = null)` - Returns translation for specified locale or app locale

### Implementation Pattern

**Main Model Example** (e.g., `Post.php`):
```php
public function translations(): HasMany
{
    return $this->hasMany(PostTranslation::class);
}

public function translation(?string $locale = null): ?PostTranslation
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}
```

**Translation Model** (e.g., `PostTranslation.php`):
```php
protected $fillable = [
    'post_id',
    'locale',
    'title',
    'slug',
    'content',
    'excerpt',
    'meta_title',
    'meta_description',
    'meta_keywords',
];
```

### Current Translatable Models
- `Post` / `PostTranslation`
- `Category` / `CategoryTranslation`
- `Tag` / `TagTranslation`
- `Setting` / `SettingTranslation`

### Managing Translations in Filament

Use **Repeater** component for translations in forms:
```php
Forms\Components\Repeater::make('translations')
    ->schema([
        Forms\Components\Select::make('locale')
            ->options(['tr' => 'Türkçe', 'en' => 'English'])
            ->required()
            ->distinct(),
        Forms\Components\TextInput::make('title')
            ->required(),
        // ... other translatable fields
    ])
    ->itemLabel(fn (array $state): ?string => $state['locale'] ?? null)
    ->collapsible()
```

**[⬆ Back to Top](#table-of-contents)**

---

## Project Structure

### Filament Configuration
- **Panel Path:** `/admin`
- **Panel ID:** `admin`
- **Primary Color:** Amber
- **Max Content Width:** Full
- **Features:** Collapsible sidebar on desktop, auto-discovery of resources/pages/widgets
- **Provider:** [app/Providers/Filament/AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php)

### Directory Structure
- `app/Filament/Resources/` - Filament CRUD resources (auto-discovered)
- `app/Filament/Pages/` - Custom Filament pages (auto-discovered)
- `app/Filament/Widgets/` - Dashboard widgets (auto-discovered)
- `app/Models/` - Eloquent models with translation relationships
- `database/migrations/` - Database migrations
- `tests/Feature/` - Feature tests (Pest)
- `tests/Unit/` - Unit tests (Pest)

### Laravel 12 Structure Notes
- No `app/Console/Kernel.php` - use `bootstrap/app.php` or `routes/console.php`
- No middleware files in `app/Http/Middleware/` - register in `bootstrap/app.php`
- Commands auto-register from `app/Console/Commands/`
- Model casts use `casts()` method instead of `$casts` property

**[⬆ Back to Top](#table-of-contents)**

---

## Filament Resource Creation

Always use Artisan commands to create Filament resources:

```bash
# Create a resource with all pages
php artisan make:filament-resource ModelName --generate --no-interaction

# Create a simple resource (modal-based)
php artisan make:filament-resource ModelName --simple --no-interaction

# Create a custom page
php artisan make:filament-page PageName --no-interaction

# Create a resource page
php artisan make:filament-page PageName --resource=ResourceName --type=ListRecords --no-interaction
```

### Creating Dashboard Widgets

```bash
# Stats widget
php artisan make:filament-widget WidgetName --stats --no-interaction

# Chart widget
php artisan make:filament-widget WidgetName --chart --no-interaction

# Table widget
php artisan make:filament-widget WidgetName --table --no-interaction
```

### TinyEditor Usage

This project uses `amidesfahani/filament-tinyeditor` for rich text editing:

```php
use AmidEsfahani\FilamentTinyEditor\TinyEditor;

TinyEditor::make('content')
    ->fileAttachmentsDisk('public')
    ->fileAttachmentsDirectory('uploads')
    ->profile('default|simple|full|minimal|....')
    ->required();
```

**[⬆ Back to Top](#table-of-contents)**

---

## Database & Queues

- **Default Connection:** SQLite (`database/database.sqlite`)
- **Queue Driver:** Database
- **Cache Driver:** Database
- **Session Driver:** Database

For production, consider switching to MySQL/PostgreSQL and Redis for better performance.

**[⬆ Back to Top](#table-of-contents)**

---

## Testing Conventions

- Use Pest for all tests
- Feature tests go in `tests/Feature/`
- Unit tests go in `tests/Unit/`
- Always test Filament resources with Livewire assertions
- Use factories for creating test data

### Example Filament Resource Test
```php
use function Pest\Livewire\livewire;

it('can list records', function () {
    $records = Model::factory()->count(10)->create();

    livewire(ListModels::class)
        ->assertCanSeeTableRecords($records);
});

it('can create record', function () {
    livewire(CreateModel::class)
        ->fillForm([
            'name' => 'Test Name',
        ])
        ->call('create')
        ->assertNotified();

    expect(Model::where('name', 'Test Name')->exists())->toBeTrue();
});
```

**[⬆ Back to Top](#table-of-contents)**

---

## Common Patterns

### Creating a Complete CRUD Feature

1. **Create Model with Migration and Factory:**
```bash
php artisan make:model ModelName -mf --no-interaction
```

2. **Create Filament Resource:**
```bash
php artisan make:filament-resource ModelName --generate --no-interaction
```

3. **Create Tests:**
```bash
php artisan make:test --pest Feature/ModelNameTest --no-interaction
```

4. **Run Pint:**
```bash
vendor/bin/pint --dirty
```

5. **Run Tests:**
```bash
php artisan test --filter=ModelName
```

### Creating a Translatable Model

1. **Create Main Model and Translation Model:**
```bash
php artisan make:model ModelName -mf --no-interaction
php artisan make:model ModelNameTranslation -mf --no-interaction
```

2. **Update Main Model:**
   - Add `translations()` HasMany relationship
   - Add `translation(?string $locale)` helper method

3. **Create Migrations:**
   - Main table: language-independent fields (IDs, slugs, images, etc.)
   - Translation table: `model_name_id`, `locale`, and translatable fields (title, content, etc.)

4. **Update Filament Resource:**
   - Use Repeater for translations field
   - Configure with locale selector and translatable fields
   - Add `->collapsible()` for better UX

5. **Create Factory and Tests:**
```bash
php artisan make:test --pest Feature/ModelNameTest --no-interaction
```

### Creating a Custom Filament Page with Form

For singleton patterns like Settings:

1. **Create Custom Page:**
```bash
php artisan make:filament-page ManageModelName --no-interaction
```

2. **Add Form and Actions:**
   - Define `form(Form $form)` method with schema
   - Define `getFormActions()` for save button
   - Implement save logic manually
   - Use Repeater for translations if needed

3. **Example Structure:**
```php
public ?array $data = [];

public function mount(): void
{
    $record = Model::with('translations')->first();
    $this->form->fill($record->toArray());
}

public function form(Form $form): Form
{
    return $form->schema([...])->statePath('data');
}

public function save(): void
{
    $data = $this->form->getState();
    // Manual save logic with translations
}
```

**[⬆ Back to Top](#table-of-contents)**

---

## Important Notes

- Filament panel is at `/admin` path
- Always run `vendor/bin/pint --dirty` before committing code
- Use Laravel Boost MCP tools for documentation and debugging
- Models should use `casts()` method for type casting (Laravel 12 convention)
- All Filament components use fluent method chaining
- Session, cache, and queue use database driver by default
- Translation pattern uses separate models for translatable content
- Settings use singleton pattern with custom Filament page

**[⬆ Back to Top](#table-of-contents)**

---

## Troubleshooting

### Frontend Changes Not Reflecting
If frontend changes aren't visible, the build might need to be refreshed:
```bash
npm run build  # or ask user to run npm run dev
```

### Vite Manifest Error
If you see "Unable to locate file in Vite manifest" error:
```bash
npm run build  # or npm run dev / composer run dev
```

### Queue Not Processing
Make sure queue worker is running:
```bash
php artisan queue:listen
# or
composer run dev  # includes queue worker
```

**[⬆ Back to Top](#table-of-contents)**

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.12
- filament/filament (FILAMENT) - v3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@volt` directive.
- You must check existing Volt components to determine if they're functional or class based. If you can't detect that, ask the user which they prefer before writing a Volt component.

### Volt Functional Component Example

<code-snippet name="Volt Functional Component Example" lang="php">
@volt
<?php
use function Livewire\Volt\{state, computed};

state(['count' => 0]);

$increment = fn () => $this->count++;
$decrement = fn () => $this->count--;

$double = computed(fn () => $this->count * 2);
?>

<div>
    <h1>Count: {{ $count }}</h1>
    <h2>Double: {{ $this->double }}</h2>
    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
</div>
@endvolt
</code-snippet>


### Volt Class Based Component Example
To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:


<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>


### Testing Volt & Volt Components
- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
</code-snippet>


<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
</code-snippet>


### Common Patterns


<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>

<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>

<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== pest/v4 rules ===

## Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>

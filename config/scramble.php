<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => '# Headless CMS API

Public readonly API for Next.js frontend application.

## Features
- 🌍 **Multi-language Support**: Content available in multiple languages (Turkish, English)
- 📝 **Content Types**: Posts (blogs and pages), categories, tags, and settings
- 🔒 **Readonly**: All endpoints are GET requests only
- 🚀 **Fast & Efficient**: Optimized for performance with pagination
- 📦 **RESTful**: Clean and intuitive API design

## Authentication
**No authentication required** - All endpoints are publicly accessible.

## Localization
Set language using one of the following methods:

1. **Query Parameter** (Recommended for Next.js):
   ```
   GET /api/posts?locale=tr
   ```

2. **HTTP Header**:
   ```
   Accept-Language: tr
   ```

**Supported Locales**: `tr` (Turkish), `en` (English)
**Default Locale**: `tr`

## Pagination
All list endpoints support pagination with these parameters:
- `per_page`: Number of items per page (default: 20, max: 100)
- `page`: Page number (default: 1)

## Response Format
All responses follow this structure:
```json
{
  "data": [...],  // Resource or array of resources
  "meta": {       // Pagination metadata (for collections)
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 20
  }
}
```

## Example Usage (Next.js)

### Fetching Posts
```typescript
const response = await fetch("http://localhost:8000/api/posts?locale=tr&type=blog&per_page=10");
const { data, meta } = await response.json();
```

### Fetching Single Post
```typescript
const response = await fetch("http://localhost:8000/api/posts/my-slug?locale=tr");
const { data } = await response.json();
```

### Fetching Settings
```typescript
const response = await fetch("http://localhost:8000/api/settings?locale=tr");
const { data } = await response.json();
// Access: data.logo, data.translation.site_name, etc.
```

## Rate Limiting
Currently no rate limiting applied. May be added in production.

## CORS
CORS is configured to allow requests from:
- `http://localhost:3000` (Next.js dev)
- `http://localhost:3001`
- Custom domain (set via `FRONTEND_URL` env variable)

## Support
For issues or questions, contact: '.env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Define the title of the documentation's website. App name is used when this config is `null`.
         */
        'title' => null,

        /*
         * Define the theme of the documentation. Available options are `light`, `dark`, and `system`.
         */
        'theme' => 'light',

        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * Hide the schemas in the Table of Contents. Enabled by default.
         */
        'hide_schemas' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',

        /*
         * There are three layouts for Elements:
         * - sidebar - (Elements default) Three-column design with a sidebar that can be resized.
         * - responsive - Like sidebar, except at small screen sizes it collapses the sidebar into a drawer that can be toggled open.
         * - stacked - Everything in a single column, making integrations with existing websites that have their own sidebar or other columns already.
         */
        'layout' => 'responsive',
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => [
        'Local Development' => env('APP_URL', 'http://localhost').'/api',
        'Production' => env('PRODUCTION_API_URL', 'https://api.example.com'),
    ],

    /**
     * Determines how Scramble stores the descriptions of enum cases.
     * Available options:
     * - 'description' – Case descriptions are stored as the enum schema's description using table formatting.
     * - 'extension' – Case descriptions are stored in the `x-enumDescriptions` enum schema extension.
     *
     *    @see https://redocly.com/docs-legacy/api-reference-docs/specification-extensions/x-enum-descriptions
     * - false - Case descriptions are ignored.
     */
    'enum_cases_description_strategy' => 'description',

    /**
     * Determines how Scramble stores the names of enum cases.
     * Available options:
     * - 'names' – Case names are stored in the `x-enumNames` enum schema extension.
     * - 'varnames' - Case names are stored in the `x-enum-varnames` enum schema extension.
     * - false - Case names are not stored.
     */
    'enum_cases_names_strategy' => false,

    /**
     * When Scramble encounters deep objects in query parameters, it flattens the parameters so the generated
     * OpenAPI document correctly describes the API. Flattening deep query parameters is relevant until
     * OpenAPI 3.2 is released and query string structure can be described properly.
     *
     * For example, this nested validation rule describes the object with `bar` property:
     * `['foo.bar' => ['required', 'int']]`.
     *
     * When `flatten_deep_query_parameters` is `true`, Scramble will document the parameter like so:
     * `{"name":"foo[bar]", "schema":{"type":"int"}, "required":true}`.
     *
     * When `flatten_deep_query_parameters` is `false`, Scramble will document the parameter like so:
     *  `{"name":"foo", "schema": {"type":"object", "properties":{"bar":{"type": "int"}}, "required": ["bar"]}, "required":true}`.
     */
    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'extensions' => [
        'x-api-type' => 'readonly',
        'x-language-support' => ['tr', 'en'],
        'x-default-locale' => 'tr',
        'x-pagination' => [
            'default_per_page' => 20,
            'max_per_page' => 100,
        ],
        'x-content-types' => [
            'posts' => [
                'types' => ['blog', 'page'],
                'statuses' => ['draft', 'published', 'scheduled'],
            ],
        ],
    ],
];

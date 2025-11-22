# Public Frontend (Volt)

This document describes the public-facing website built using Livewire Volt.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Routes Structure](#routes-structure)
2. [Volt Components](#volt-components)
3. [Volt Component Pattern](#volt-component-pattern)
4. [Language Switching](#language-switching)

---

## Routes Structure

Routes are defined in `routes/web.php`:

### English Routes (Default - no prefix)

- `/` - Home page
- `/contact` - Contact page
- `/services` - Services listing
- `/services/{slug}` - Individual service detail

### Turkish Routes (`/tr` prefix)

- `/tr/` - Ana sayfa (Home)
- `/tr/iletisim` - İletişim (Contact)
- `/tr/hizmetler` - Hizmetler (Services)
- `/tr/hizmetler/{slug}` - Hizmet detayı (Service detail)

**[⬆ Back to Top](#table-of-contents)**

---

## Volt Components

Volt components are located in `resources/views/livewire/`:

- `home.blade.php` - Homepage with hero, about, services, blog, and contact sections
- `contact.blade.php` - Contact page
- `services.blade.php` - Services listing page
- `service.blade.php` - Individual service detail page

**[⬆ Back to Top](#table-of-contents)**

---

## Volt Component Pattern

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

**[⬆ Back to Top](#table-of-contents)**

---

## Language Switching

- Language is determined by URL prefix (`/tr/*` for Turkish, no prefix for English)
- Translation strings use Laravel's `__()` helper (e.g., `__('home.hero.title')`)
- Translation files located in `lang/en/` and `lang/tr/`
- Content models use `translation(?string $locale)` helper to get localized content

**[⬆ Back to Top](#table-of-contents)**

---

**[← Back to Main Documentation](../CLAUDE.md)**

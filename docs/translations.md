# Multi-Language Translation Pattern

This document describes the custom translation pattern used across all content models.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Structure](#structure)
2. [Implementation Pattern](#implementation-pattern)
3. [Current Translatable Models](#current-translatable-models)
4. [Managing Translations in Filament](#managing-translations-in-filament)

---

## Structure

All content models use a custom translation pattern:

- **Main Model**: Stores language-independent data (IDs, slugs, images, foreign keys, etc.)
- **Translation Model**: Stores language-specific data with `locale` field (e.g., title, content, meta_description)
- **Relationship**: `translations()` HasMany relationship on main model
- **Helper Method**: `translation(?string $locale = null)` - Returns translation for specified locale or app locale

**[⬆ Back to Top](#table-of-contents)**

---

## Implementation Pattern

### Main Model Example

Example from `Post.php`:

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

### Translation Model Example

Example from `PostTranslation.php`:

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

**[⬆ Back to Top](#table-of-contents)**

---

## Current Translatable Models

- `Post` / `PostTranslation`
- `Category` / `CategoryTranslation`
- `Tag` / `TagTranslation`
- `Setting` / `SettingTranslation`

**[⬆ Back to Top](#table-of-contents)**

---

## Managing Translations in Filament

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

**[← Back to Main Documentation](../CLAUDE.md)**

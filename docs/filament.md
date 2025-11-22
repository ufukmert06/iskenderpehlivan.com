# Filament Guide

This document covers Filament resources, pages, widgets, and common patterns.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Filament Resources](#filament-resources)
2. [Filament Pages](#filament-pages)
3. [Dashboard Widgets](#dashboard-widgets)
4. [Creating Resources](#creating-resources)
5. [Creating Widgets](#creating-widgets)
6. [TinyEditor Usage](#tinyeditor-usage)

---

## Filament Resources

The following Filament resources manage content:

- **BlogResource**: Manages blog posts (Post model where type='blog')
- **PageResource**: Manages pages (Post model where type='page')
- **PostResource**: General post management
- **CategoryResource**: Category management with translations
- **TagResource**: Tag management with translations and color coding

**[⬆ Back to Top](#table-of-contents)**

---

## Filament Pages

### ManageSettings

Custom page for managing global site settings:

- Uses singleton pattern (one Setting record)
- Repeater field for managing translations
- Manual save logic for handling translations

**[⬆ Back to Top](#table-of-contents)**

---

## Dashboard Widgets

Located in `app/Filament/Widgets/` (auto-discovered):

- **StatsOverview**: Overview statistics cards
- **PostsByStatusChart**: Chart showing posts by status
- **PostsByTypeChart**: Chart showing posts by type
- **LatestPosts**: Table widget showing recent posts

**[⬆ Back to Top](#table-of-contents)**

---

## Creating Resources

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

**[⬆ Back to Top](#table-of-contents)**

---

## Creating Widgets

```bash
# Stats widget
php artisan make:filament-widget WidgetName --stats --no-interaction

# Chart widget
php artisan make:filament-widget WidgetName --chart --no-interaction

# Table widget
php artisan make:filament-widget WidgetName --table --no-interaction
```

**[⬆ Back to Top](#table-of-contents)**

---

## TinyEditor Usage

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

**[← Back to Main Documentation](../CLAUDE.md)**

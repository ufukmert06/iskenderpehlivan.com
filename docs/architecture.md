# CMS Architecture

This document describes the Content Management System architecture, including models, relationships, and project structure.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Content Models](#content-models)
2. [Project Structure](#project-structure)
3. [Laravel 12 Structure Notes](#laravel-12-structure-notes)

---

## Content Models

This application is a multi-language Content Management System with the following structure:

### Posts

Main content model with type differentiation:

- **Fields:** `type` (blog/page), `status`, `slug_base`, `featured_image`, `user_id`, `sort_order`
- **Relationships:**
  - `user()` - Belongs to User (author)
  - `translations()` - Has many PostTranslation
  - `categories()` - Belongs to many Category
  - `tags()` - Belongs to many Tag
- **Helper:** `translation(?string $locale)` - Get translation for specific locale

### Categories

For organizing content:

- **Fields:** `slug_base`, `sort_order`
- **Relationships:**
  - `translations()` - Has many CategoryTranslation
  - `posts()` - Belongs to many Post
- **Helper:** `translation(?string $locale)`

### Tags

For tagging content:

- **Fields:** `type`, `slug_base`, `color`, `sort_order`
- **Relationships:**
  - `translations()` - Has many TagTranslation
  - `posts()` - Belongs to many Post
- **Helper:** `translation(?string $locale)`

### Settings

Global site settings (Singleton pattern):

- **Fields:** `logo`, `favicon`, contact info, social media links, `maintenance_mode`
- **Relationships:** `translations()` - Has many SettingTranslation
- **Management:** Custom Filament Page (`ManageSettings`) with Repeater for translations
- **Pattern:** Single record in database, managed via custom page, not a resource

**[⬆ Back to Top](#table-of-contents)**

---

## Project Structure

### Filament Configuration

- **Panel Path:** `/admin`
- **Panel ID:** `admin`
- **Primary Color:** Amber
- **Max Content Width:** Full
- **Features:** Collapsible sidebar on desktop, auto-discovery of resources/pages/widgets
- **Provider:** [app/Providers/Filament/AdminPanelProvider.php](../app/Providers/Filament/AdminPanelProvider.php)

### Directory Structure

- `app/Filament/Resources/` - Filament CRUD resources (auto-discovered)
- `app/Filament/Pages/` - Custom Filament pages (auto-discovered)
- `app/Filament/Widgets/` - Dashboard widgets (auto-discovered)
- `app/Models/` - Eloquent models with translation relationships
- `database/migrations/` - Database migrations
- `tests/Feature/` - Feature tests (Pest)
- `tests/Unit/` - Unit tests (Pest)

**[⬆ Back to Top](#table-of-contents)**

---

## Laravel 12 Structure Notes

- No `app/Console/Kernel.php` - use `bootstrap/app.php` or `routes/console.php`
- No middleware files in `app/Http/Middleware/` - register in `bootstrap/app.php`
- Commands auto-register from `app/Console/Commands/`
- Model casts use `casts()` method instead of `$casts` property

**[⬆ Back to Top](#table-of-contents)**

---

**[← Back to Main Documentation](../CLAUDE.md)**

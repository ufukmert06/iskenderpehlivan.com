# Development Guide

This document covers development setup, commands, and troubleshooting.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Initial Setup](#initial-setup)
2. [Development Server](#development-server)
3. [Testing](#testing)
4. [Code Formatting](#code-formatting)
5. [Frontend Build](#frontend-build)
6. [Database & Queues](#database--queues)
7. [Troubleshooting](#troubleshooting)

---

## Initial Setup

```bash
composer run setup
```

This command installs dependencies, copies .env.example to .env, generates app key, runs migrations, and builds frontend assets.

**[⬆ Back to Top](#table-of-contents)**

---

## Development Server

```bash
composer run dev
```

Runs three processes concurrently:
- Laravel development server (`php artisan serve`)
- Queue worker (`php artisan queue:listen --tries=1`)
- Vite dev server (`npm run dev`)

### Run Services Individually

```bash
php artisan serve          # Start Laravel server
php artisan queue:listen   # Start queue worker
npm run dev               # Start Vite dev server
```

**[⬆ Back to Top](#table-of-contents)**

---

## Testing

```bash
composer run test          # Run all tests
php artisan test          # Run all tests (alternative)
php artisan test --filter=testName  # Run specific test
php artisan test tests/Feature/ExampleTest.php  # Run specific file
```

See [Testing Guide](./testing.md) for detailed testing conventions and patterns.

**[⬆ Back to Top](#table-of-contents)**

---

## Code Formatting

```bash
vendor/bin/pint --dirty   # Format modified files
vendor/bin/pint          # Format all files
```

**Always run Pint before committing code.**

**[⬆ Back to Top](#table-of-contents)**

---

## Frontend Build

```bash
npm run build  # Production build
npm run dev    # Development mode with hot reload
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

### Cache Issues

Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**[⬆ Back to Top](#table-of-contents)**

---

**[← Back to Main Documentation](../CLAUDE.md)**

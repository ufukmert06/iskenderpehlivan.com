# Testing Guide

This document covers testing conventions and patterns using Pest.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Testing Conventions](#testing-conventions)
2. [Example Tests](#example-tests)
3. [Running Tests](#running-tests)

---

## Testing Conventions

- Use Pest for all tests
- Feature tests go in `tests/Feature/`
- Unit tests go in `tests/Unit/`
- Always test Filament resources with Livewire assertions
- Use factories for creating test data

**[⬆ Back to Top](#table-of-contents)**

---

## Example Tests

### Filament Resource Test

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

### Volt Component Test

```php
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
```

**[⬆ Back to Top](#table-of-contents)**

---

## Running Tests

```bash
composer run test          # Run all tests
php artisan test          # Run all tests (alternative)
php artisan test --filter=testName  # Run specific test
php artisan test tests/Feature/ExampleTest.php  # Run specific file
```

**[⬆ Back to Top](#table-of-contents)**

---

**[← Back to Main Documentation](../CLAUDE.md)**

# Common Patterns

This document contains common patterns and workflows for development.

**[← Back to Main Documentation](../CLAUDE.md)**

---

## Table of Contents

1. [Creating a Complete CRUD Feature](#creating-a-complete-crud-feature)
2. [Creating a Translatable Model](#creating-a-translatable-model)
3. [Creating a Custom Filament Page](#creating-a-custom-filament-page-with-form)

---

## Creating a Complete CRUD Feature

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

**[⬆ Back to Top](#table-of-contents)**

---

## Creating a Translatable Model

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

**[⬆ Back to Top](#table-of-contents)**

---

## Creating a Custom Filament Page with Form

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

**[← Back to Main Documentation](../CLAUDE.md)**

# [TASK-2-011] Filament Category Resource

## Overview
Build the Filament admin resource for managing categories. Simpler than the product
resource — categories are few and rarely need bulk changes.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 1–2 hrs
**Depends on:** TASK-2-003
**Blocks:** Nothing

---

## Files to Create

```
app/Filament/Resources/
├── CategoryResource.php
└── CategoryResource/
    └── Pages/
        ├── ListCategories.php
        ├── CreateCategory.php
        └── EditCategory.php
```

---

## Generate Stubs

```bash
php artisan make:filament-resource Category --generate
```

---

## CategoryResource.php — Key Sections

### Table columns
```php
Tables\Columns\TextColumn::make('sort_order')->sortable()->label('#'),
Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
Tables\Columns\TextColumn::make('slug')->searchable(),
Tables\Columns\TextColumn::make('products_count')
    ->counts('products')
    ->label('Products')
    ->sortable(),
Tables\Columns\IconColumn::make('active')->boolean()->sortable(),
```

### Table actions
```php
Tables\Actions\EditAction::make(),
Tables\Actions\Action::make('view_frontend')
    ->label('View Page')
    ->icon('heroicon-o-arrow-top-right-on-square')
    ->url(fn ($record) => route('categories.show', $record->slug))
    ->openUrlInNewTab(),
```

### Form schema
```php
Forms\Components\Section::make('Category Details')->schema([
    Forms\Components\TextInput::make('name')->required()->maxLength(100)
        ->live(onBlur: true)
        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
    Forms\Components\TextInput::make('slug')->required()->maxLength(100)->unique(ignoreRecord: true),
    Forms\Components\Select::make('parent_id')
        ->relationship('parent', 'name')
        ->nullable()
        ->helperText('Leave blank for top-level category. Currently unused.'),
    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
    Forms\Components\Toggle::make('active')->default(true),
]),

Forms\Components\Section::make('Category Page Content')->schema([
    Forms\Components\TextInput::make('hero_title')->maxLength(200)
        ->helperText('H1 heading on the category page. Defaults to category name if blank.'),
    Forms\Components\Textarea::make('description')->rows(4)
        ->helperText('Intro paragraph shown below the hero. Supports plain text.'),
]),
```

---

## Acceptance Criteria
- [ ] Category list shows all 6 seeded categories with product count column
- [ ] `sort_order` column is sortable and reflects actual order
- [ ] "View Page" action opens the frontend category page in a new tab
- [ ] Creating a category auto-generates slug from name
- [ ] Editing `hero_title` and `description` reflects on the category frontend page
- [ ] `parent_id` field is available but defaults to null

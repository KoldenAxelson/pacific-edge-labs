# [TASK-2-010] Filament Product Resource

## Overview
Build the Filament admin resource for managing products: list, create, edit, view.
Includes image upload, research link management, and bulk activate/deactivate.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-003
**Blocks:** Nothing

---

## Files to Create

```
app/Filament/Resources/
├── ProductResource.php
└── ProductResource/
    └── Pages/
        ├── ListProducts.php
        ├── CreateProduct.php
        └── EditProduct.php
```

---

## Generate Stubs

```bash
php artisan make:filament-resource Product --generate
```

---

## ProductResource.php — Key Sections

### Table columns
```php
Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
Tables\Columns\TextColumn::make('name')->searchable()->sortable()->limit(40),
Tables\Columns\TextColumn::make('category.name')->sortable()->badge(),
Tables\Columns\TextColumn::make('price')->money('USD')->sortable(),
Tables\Columns\IconColumn::make('active')->boolean()->sortable(),
Tables\Columns\IconColumn::make('featured')->boolean(),
Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
```

### Table filters
```php
Tables\Filters\SelectFilter::make('category')
    ->relationship('category', 'name'),
Tables\Filters\TernaryFilter::make('active'),
Tables\Filters\TernaryFilter::make('featured'),
Tables\Filters\TrashedFilter::make(),  // soft deletes
```

### Bulk actions
```php
Tables\Actions\BulkActionGroup::make([
    Tables\Actions\BulkAction::make('activate')
        ->action(fn ($records) => $records->each->update(['active' => true]))
        ->requiresConfirmation()
        ->icon('heroicon-o-check-circle'),
    Tables\Actions\BulkAction::make('deactivate')
        ->action(fn ($records) => $records->each->update(['active' => false]))
        ->requiresConfirmation()
        ->icon('heroicon-o-x-circle'),
    Tables\Actions\DeleteBulkAction::make(),
    Tables\Actions\RestoreBulkAction::make(),
]),
```

### Form schema
```php
Forms\Components\Section::make('Core Details')->schema([
    Forms\Components\Select::make('category_id')
        ->relationship('category', 'name')
        ->required()
        ->searchable(),
    Forms\Components\TextInput::make('name')->required()->maxLength(200)
        ->live(onBlur: true)
        ->afterStateUpdated(fn ($state, $set) =>
            $set('slug', Str::slug($state))
        ),
    Forms\Components\TextInput::make('slug')->required()->maxLength(200)->unique(ignoreRecord: true),
    Forms\Components\TextInput::make('sku')->required()->maxLength(30)->unique(ignoreRecord: true),
    Forms\Components\Grid::make(2)->schema([
        Forms\Components\TextInput::make('price')->required()->numeric()->prefix('$'),
        Forms\Components\TextInput::make('compare_price')->numeric()->prefix('$'),
    ]),
    Forms\Components\Toggle::make('active')->default(true),
    Forms\Components\Toggle::make('featured')->default(false),
]),

Forms\Components\Section::make('Content')->schema([
    Forms\Components\Textarea::make('short_description')->rows(3)->maxLength(500)
        ->helperText('Shown on product cards. Max 500 characters.'),
    Forms\Components\RichEditor::make('description')->required()
        ->disableToolbarButtons(['attachFiles', 'codeBlock']),
]),

Forms\Components\Section::make('Specifications')->schema([
    Forms\Components\TextInput::make('form')->maxLength(100),
    Forms\Components\TextInput::make('concentration')->maxLength(100),
    Forms\Components\Textarea::make('storage_conditions')->rows(2)->maxLength(300),
]),

Forms\Components\Section::make('SEO Meta (optional)')->schema([
    Forms\Components\TextInput::make('meta_title')->maxLength(200)
        ->helperText('Leave blank to auto-generate from product name.'),
    Forms\Components\Textarea::make('meta_description')->rows(3)
        ->helperText('Leave blank to auto-generate from short description.'),
])->collapsed(),

Forms\Components\Section::make('Research Links')->schema([
    Forms\Components\Repeater::make('researchLinks')
        ->relationship()
        ->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(300)->columnSpanFull(),
            Forms\Components\TextInput::make('authors')->maxLength(500),
            Forms\Components\TextInput::make('publication_year')->numeric()->minValue(1950)->maxValue(2030),
            Forms\Components\TextInput::make('journal')->maxLength(200),
            Forms\Components\TextInput::make('pubmed_id')->maxLength(20)->label('PubMed ID'),
            Forms\Components\TextInput::make('url')->required()->url()->maxLength(500)->columnSpanFull(),
        ])
        ->columns(2)
        ->orderColumn('sort_order')
        ->reorderable()
        ->collapsible(),
]),
```

---

## Acceptance Criteria
- [ ] Product list shows all 30 seeded products with correct columns
- [ ] Category filter, active filter, and trashed filter all work
- [ ] Creating a new product auto-generates slug from name
- [ ] Editing a product saves all fields including research links
- [ ] Bulk activate/deactivate works on multiple selected records
- [ ] Soft-deleted products visible under TrashedFilter and can be restored
- [ ] SEO meta fields collapse by default but expand when clicked
- [ ] Research links are reorderable via drag handle

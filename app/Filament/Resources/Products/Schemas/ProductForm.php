<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Core Details')->schema([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(200)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', Str::slug($state))
                        ),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(200)
                        ->unique(ignoreRecord: true),
                    TextInput::make('sku')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true)
                        ->label('SKU'),
                    Grid::make(2)->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('compare_price')
                            ->numeric()
                            ->prefix('$')
                            ->label('Compare-at Price'),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('active')
                            ->default(true),
                        Toggle::make('featured')
                            ->default(false),
                    ]),
                ]),

                Section::make('Content')->schema([
                    Textarea::make('short_description')
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('Shown on product cards. Max 500 characters.'),
                    RichEditor::make('description')
                        ->required()
                        ->disableToolbarButtons(['attachFiles', 'codeBlock']),
                ]),

                Section::make('Specifications')->schema([
                    TextInput::make('form')
                        ->maxLength(100),
                    TextInput::make('concentration')
                        ->maxLength(100),
                    Textarea::make('storage_conditions')
                        ->rows(2)
                        ->maxLength(300),
                ]),

                Section::make('SEO Meta (optional)')
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(200)
                            ->helperText('Leave blank to auto-generate from product name.'),
                        Textarea::make('meta_description')
                            ->rows(3)
                            ->helperText('Leave blank to auto-generate from short description.'),
                    ]),

                Section::make('Research Links')->schema([
                    Repeater::make('researchLinks')
                        ->relationship()
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(300)
                                ->columnSpanFull(),
                            TextInput::make('authors')
                                ->maxLength(500),
                            TextInput::make('publication_year')
                                ->numeric()
                                ->minValue(1950)
                                ->maxValue(2030),
                            TextInput::make('journal')
                                ->maxLength(200),
                            TextInput::make('pubmed_id')
                                ->maxLength(20)
                                ->label('PubMed ID'),
                            TextInput::make('url')
                                ->required()
                                ->url()
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(0),
                ]),
            ]);
    }
}

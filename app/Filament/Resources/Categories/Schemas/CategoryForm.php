<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', Str::slug($state))
                        ),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Select::make('parent_id')
                        ->relationship('parent', 'name')
                        ->nullable()
                        ->helperText('Leave blank for top-level category. Currently unused.'),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('active')
                        ->default(true),
                ]),

                Section::make('Category Page Content')->schema([
                    TextInput::make('hero_title')
                        ->maxLength(200)
                        ->helperText('H1 heading on the category page. Defaults to category name if blank.'),
                    Textarea::make('description')
                        ->rows(4)
                        ->helperText('Intro paragraph shown below the hero. Supports plain text.'),
                ]),
            ]);
    }
}

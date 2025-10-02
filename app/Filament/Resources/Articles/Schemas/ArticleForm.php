<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->url()
                    ->required(),
                DatePicker::make('published_at')
                    ->required(),
                Select::make('news_outlet_id')
                    ->relationship('newsOutlet', 'name'),
                Select::make('genres')
                    ->relationship('genres', 'name')
                    ->multiple()
                    ->preload(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}

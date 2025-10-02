<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article Details')
                    ->schema([
                        TextEntry::make('genres.name')
                            ->label('Genres')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('newsOutlet.name')
                            ->label('News outlet')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('published_at')
                            ->date(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('url'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Content')
                    ->schema([
                        TextEntry::make('content')
                            ->markdown()
                            ->prose()
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

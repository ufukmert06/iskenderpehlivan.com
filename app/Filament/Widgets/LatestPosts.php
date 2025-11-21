<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPosts extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Son Eklenen Yazılar';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->with(['user', 'categories', 'tags'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('slug_base')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'blog' => 'success',
                        'page' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Yazar')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories_count')
                    ->label('Kategori')
                    ->counts('categories')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tags_count')
                    ->label('Etiket')
                    ->counts('tags')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Düzenle')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary'),

                Tables\Actions\Action::make('view')
                    ->label('Görüntüle')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-eye')
                    ->color('gray'),
            ]);
    }
}

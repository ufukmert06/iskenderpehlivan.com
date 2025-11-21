<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Etiket';

    protected static ?string $pluralModelLabel = 'Etiketler';

    protected static ?string $navigationLabel = 'Etiketler';

    protected static ?string $navigationGroup = 'İçerik';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tip')
                    ->options([
                        'post' => 'Yazı',
                        'category' => 'Kategori',
                    ])
                    ->required()
                    ->default('post')
                    ->native(false),

                Forms\Components\TextInput::make('slug_base')
                    ->label('Temel Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Dil bağımsız temel slug (ilk çeviriden otomatik üretilir)')
                    ->dehydrated(),

                Forms\Components\ColorPicker::make('color')
                    ->label('Renk')
                    ->helperText('Etiket için özel renk seçebilirsiniz'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Section::make('Etiket Çevirileri')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('Dil')
                                    ->options([
                                        'tr' => 'Türkçe',
                                        'en' => 'English',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('name')
                                    ->label('Ad')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));

                                        // İlk çeviride slug_base'i de güncelle
                                        if ($get('../../slug_base') === Str::slug($old ?? '') || empty($get('../../slug_base'))) {
                                            $set('../../slug_base', Str::slug($state));
                                        }
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Dile özel URL slug (otomatik üretilir, manuel düzenlenebilir)'),

                                Forms\Components\Textarea::make('description')
                                    ->label('Açıklama')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['locale'] ?? null)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->reorderable(false)
                            ->addActionLabel('Yeni Çeviri Ekle'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug_base')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'post' => 'success',
                        'category' => 'info',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Renk')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Yazı Sayısı')
                    ->counts('posts')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        'post' => 'Yazı',
                        'category' => 'Kategori',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}

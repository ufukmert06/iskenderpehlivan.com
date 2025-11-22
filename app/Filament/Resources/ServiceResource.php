<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Hizmetler';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'service');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hizmet Bilgileri')
                    ->schema([
                        Forms\Components\Hidden::make('type')
                            ->default('service'),
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Öne Çıkan Görsel')
                            ->image()
                            ->directory('services')
                            ->disk('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->helperText('Hizmet için öne çıkan görsel')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug_base')
                            ->label('Temel Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Dil bağımsız temel slug (ilk çeviriden otomatik üretilir)')
                            ->dehydrated(),
                        Forms\Components\TextInput::make('icon')
                            ->label('İkon')
                            ->helperText('Font Awesome icon sınıfı veya emoji'),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'draft' => 'Taslak',
                                'published' => 'Yayınlandı',
                                'archived' => 'Arşivlendi',
                            ])
                            ->required()
                            ->default('published')
                            ->native(false),
                        Forms\Components\Select::make('user_id')
                            ->label('Yazar')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->label('Sıra Numarası'),
                    ]),
                Forms\Components\Section::make('Çeviriler')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->options([
                                        'tr' => 'Türkçe',
                                        'en' => 'English',
                                    ])
                                    ->required()
                                    ->distinct()
                                    ->label('Dil')
                                    ->native(false),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->label('Hizmet Adı')
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
                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Kısa Açıklama')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TinyEditor::make('content')
                                    ->label('İçerik')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('uploads')
                                    ->profile('default')
                                    ->columnSpanFull()
                                    ->required(),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'tr' => 'Türkçe',
                                'en' => 'English',
                                default => null,
                            })
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->reorderable(false)
                            ->addActionLabel('Yeni Çeviri Ekle'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Görsel')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('slug_base')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable()
                    ->label('İkon'),
                Tables\Columns\TextColumn::make('translations.0.title')
                    ->label('Hizmet Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Durum')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Taslak',
                        'published' => 'Yayınlandı',
                        'archived' => 'Arşivlendi',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable()
                    ->label('Sıra'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Oluşturulma Tarihi')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

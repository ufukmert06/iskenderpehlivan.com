<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageCategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PageCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $modelLabel = 'Sayfa Kategorisi';

    protected static ?string $pluralModelLabel = 'Sayfa Kategorileri';

    protected static ?string $navigationLabel = 'Sayfa Kategorileri';

    protected static ?string $navigationGroup = 'Sayfa Yonetimi';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'page');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug_base')
                    ->label('Temel Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Dil bagimsiz temel slug (ilk ceviriden otomatik uretilir)')
                    ->dehydrated(),

                Forms\Components\Select::make('parent_id')
                    ->label('Ust Kategori')
                    ->relationship('parent', 'slug_base', fn (Builder $query) => $query->where('type', 'page'))
                    ->searchable()
                    ->preload()
                    ->helperText('Ust kategori secin (bos birakilabilir)'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Siralama')
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\Section::make('Kategori Cevirileri')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('Dil')
                                    ->options([
                                        'tr' => 'Turkce',
                                        'en' => 'English',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('name')
                                    ->label('Kategori Adi')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));

                                        // Ilk ceviride slug_base'i de guncelle
                                        if ($get('../../slug_base') === Str::slug($old ?? '') || empty($get('../../slug_base'))) {
                                            $set('../../slug_base', Str::slug($state));
                                        }
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Dile ozel kategori slug (otomatik uretilir, manuel duzenlenebilir)'),

                                Forms\Components\Textarea::make('description')
                                    ->label('Aciklama')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['locale'] ?? null)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->reorderable(false)
                            ->addActionLabel('Yeni Ceviri Ekle'),
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

                Tables\Columns\TextColumn::make('parent.slug_base')
                    ->label('Ust Kategori')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Ust kategori yok'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Olusturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncelleme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Duzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
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
            'index' => Pages\ListPageCategories::route('/'),
            'create' => Pages\CreatePageCategory::route('/create'),
            'edit' => Pages\EditPageCategory::route('/{record}/edit'),
        ];
    }
}

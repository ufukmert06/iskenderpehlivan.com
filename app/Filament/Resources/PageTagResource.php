<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageTagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PageTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Sayfa Etiketi';

    protected static ?string $pluralModelLabel = 'Sayfa Etiketleri';

    protected static ?string $navigationLabel = 'Sayfa Etiketleri';

    protected static ?string $navigationGroup = 'Sayfa Yonetimi';

    protected static ?int $navigationSort = 3;

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

                Forms\Components\ColorPicker::make('color')
                    ->label('Renk')
                    ->helperText('Etiket icin ozel renk secebilirsiniz'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Section::make('Etiket Cevirileri')
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
                                    ->label('Ad')
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
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Dile ozel URL slug (otomatik uretilir, manuel duzenlenebilir)'),

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

                Tables\Columns\ColorColumn::make('color')
                    ->label('Renk')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Yazi Sayisi')
                    ->counts('posts')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Olusturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncellenme')
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
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageTags::route('/'),
            'create' => Pages\CreatePageTag::route('/create'),
            'edit' => Pages\EditPageTag::route('/{record}/edit'),
        ];
    }
}

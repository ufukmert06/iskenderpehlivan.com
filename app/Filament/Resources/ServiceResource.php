<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Hizmetler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hizmet Bilgileri')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Öne Çıkan Görsel')
                            ->image()
                            ->directory('services')
                            ->disk('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->helperText('Hizmet için öne çıkan görsel')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('icon')
                            ->label('İkon')
                            ->helperText('Font Awesome icon sınıfı veya emoji'),
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
                                    ->label('Dil'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Hizmet Adı'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Açıklama'),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['locale'] ?? null) {
                                'tr' => 'Türkçe',
                                'en' => 'English',
                                default => null,
                            })
                            ->collapsible(),
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
                Tables\Columns\TextColumn::make('icon')
                    ->searchable()
                    ->label('İkon'),
                Tables\Columns\TextColumn::make('translations.0.name')
                    ->label('Hizmet Adı (EN)')
                    ->searchable(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

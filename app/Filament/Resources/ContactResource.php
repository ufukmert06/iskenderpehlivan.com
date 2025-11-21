<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $modelLabel = 'İletişim Mesajı';

    protected static ?string $pluralModelLabel = 'İletişim Mesajları';

    protected static ?string $navigationLabel = 'İletişim Mesajları';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),

                Forms\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->disabled(),

                Forms\Components\Textarea::make('message')
                    ->label('Mesaj')
                    ->required()
                    ->rows(5)
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'unread' => 'Okunmadı',
                        'read' => 'Okundu',
                        'replied' => 'Yanıtlandı',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\Placeholder::make('created_at')
                    ->label('Gönderim Tarihi')
                    ->content(fn (Contact $record): string => $record->created_at?->format('d.m.Y H:i') ?? '-'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Mesaj Detayları')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Ad Soyad'),

                        TextEntry::make('email')
                            ->label('E-posta')
                            ->copyable(),

                        TextEntry::make('status')
                            ->label('Durum')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'unread' => 'warning',
                                'read' => 'info',
                                'replied' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'unread' => 'Okunmadı',
                                'read' => 'Okundu',
                                'replied' => 'Yanıtlandı',
                                default => $state,
                            }),

                        TextEntry::make('created_at')
                            ->label('Gönderim Tarihi')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),

                Section::make('Mesaj İçeriği')
                    ->schema([
                        TextEntry::make('message')
                            ->label('Mesaj')
                            ->prose()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Mesaj')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'warning',
                        'read' => 'info',
                        'replied' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => 'Okunmadı',
                        'read' => 'Okundu',
                        'replied' => 'Yanıtlandı',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gönderim Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'unread' => 'Okunmadı',
                        'read' => 'Okundu',
                        'replied' => 'Yanıtlandı',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
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
            'index' => Pages\ListContacts::route('/'),
            'view' => Pages\ViewContact::route('/{record}'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'unread')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

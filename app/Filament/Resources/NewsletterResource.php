<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Models\Newsletter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'İletişim';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Bülten Aboneleri';
    }

    public static function getPluralLabel(): string
    {
        return 'Bülten Aboneleri';
    }

    public static function getLabel(): string
    {
        return 'Bülten Abonesi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'unsubscribed' => 'Abonelikten Çıkmış',
                    ])
                    ->required()
                    ->default('active'),
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Adresi')
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('subscribed_at')
                    ->label('Abone Olma Tarihi')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('unsubscribed_at')
                    ->label('Abonelikten Çıkma Tarihi')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'unsubscribed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'unsubscribed' => 'Abonelikten Çıkmış',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subscribed_at')
                    ->label('Abone Olma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'unsubscribed' => 'Abonelikten Çıkmış',
                    ]),
                Tables\Filters\Filter::make('subscribed_today')
                    ->label('Bugün Abone Olanlar')
                    ->query(fn (Builder $query): Builder => $query->whereDate('subscribed_at', today())),
                Tables\Filters\Filter::make('subscribed_this_week')
                    ->label('Bu Hafta Abone Olanlar')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('subscribed_at', [now()->startOfWeek(), now()->endOfWeek()])),
                Tables\Filters\Filter::make('subscribed_this_month')
                    ->label('Bu Ay Abone Olanlar')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('subscribed_at', now()->month)
                        ->whereYear('subscribed_at', now()->year)),
            ])
            ->actions([
                Tables\Actions\Action::make('unsubscribe')
                    ->label('Abonelikten Çıkar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Newsletter $record) => $record->unsubscribe())
                    ->visible(fn (Newsletter $record) => $record->isActive()),
                Tables\Actions\Action::make('resubscribe')
                    ->label('Yeniden Abone Et')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Newsletter $record) => $record->resubscribe())
                    ->visible(fn (Newsletter $record) => ! $record->isActive()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('unsubscribe')
                        ->label('Abonelikten Çıkar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->unsubscribe()),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Dışa Aktar'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletters::route('/'),
            'view' => Pages\ViewNewsletter::route('/{record}'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

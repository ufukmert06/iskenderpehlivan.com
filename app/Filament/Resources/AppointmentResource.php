<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Randevular';

    protected static ?string $modelLabel = 'Randevu';

    protected static ?string $pluralModelLabel = 'Randevular';

    protected static ?string $navigationGroup = 'İletişim';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Randevu Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('service_id')
                            ->label('Hizmet')
                            ->relationship('service', 'slug_base', fn ($query) => $query->where('type', 'service'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->translation(app()->getLocale())?->title ?? $record->slug_base)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'confirmed' => 'Onaylandı',
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tarih ve Saat')
                    ->schema([
                        Forms\Components\DatePicker::make('preferred_date')
                            ->label('Tercih Edilen Tarih')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                        Forms\Components\TimePicker::make('preferred_time')
                            ->label('Tercih Edilen Saat')
                            ->required()
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Mesaj')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Mesaj')
                            ->disabled()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Teknik Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Adresi')
                            ->disabled(),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('Tarayıcı Bilgisi')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Oluşturma Tarihi')
                            ->content(fn ($record): string => $record?->created_at?->format('d.m.Y H:i') ?? '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Güncelleme Tarihi')
                            ->content(fn ($record): string => $record?->updated_at?->format('d.m.Y H:i') ?? '-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
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
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('service.translations.title')
                    ->label('Hizmet')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->service?->translation(app()->getLocale())?->title ?? $record->service?->slug_base;
                    }),
                Tables\Columns\TextColumn::make('preferred_date')
                    ->label('Tarih')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('preferred_time')
                    ->label('Saat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                    ])
                    ->native(false),
                Tables\Filters\Filter::make('preferred_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($query, $date) => $query->whereDate('preferred_date', '>=', $date))
                            ->when($data['until'], fn ($query, $date) => $query->whereDate('preferred_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->mutateRecordDataUsing(function (array $data, $record): array {
                            // Eğer randevu beklemede ise, otomatik olarak onaylandı yap
                            if ($record->status === 'pending') {
                                $record->update(['status' => 'confirmed']);
                            }

                            return $data;
                        }),
                    Tables\Actions\Action::make('confirm')
                        ->label('Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed']))
                        ->visible(fn (Appointment $record) => $record->status === 'pending'),
                    Tables\Actions\Action::make('complete')
                        ->label('Tamamla')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Appointment $record) => $record->update(['status' => 'completed']))
                        ->visible(fn (Appointment $record) => $record->status === 'confirmed'),
                    Tables\Actions\Action::make('cancel')
                        ->label('İptal Et')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Appointment $record) => $record->update(['status' => 'cancelled']))
                        ->visible(fn (Appointment $record) => in_array($record->status, ['pending', 'confirmed'])),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm')
                        ->label('Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'confirmed'])),
                    Tables\Actions\BulkAction::make('complete')
                        ->label('Tamamla')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'completed'])),
                    Tables\Actions\BulkAction::make('cancel')
                        ->label('İptal Et')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'cancelled'])),
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAppointments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

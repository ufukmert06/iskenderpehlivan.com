<?php

namespace App\Filament\Resources\PageCategoryResource\Pages;

use App\Filament\Resources\PageCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageCategory extends EditRecord
{
    protected static string $resource = PageCategoryResource::class;

    public function getTitle(): string
    {
        return 'Sayfa Kategorisini Duzenle';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Kaydet'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil'),
        ];
    }
}

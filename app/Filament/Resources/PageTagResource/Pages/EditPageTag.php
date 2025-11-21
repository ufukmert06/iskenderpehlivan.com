<?php

namespace App\Filament\Resources\PageTagResource\Pages;

use App\Filament\Resources\PageTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageTag extends EditRecord
{
    protected static string $resource = PageTagResource::class;

    public function getTitle(): string
    {
        return 'Sayfa Etiketini Duzenle';
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

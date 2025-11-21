<?php

namespace App\Filament\Resources\PageCategoryResource\Pages;

use App\Filament\Resources\PageCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePageCategory extends CreateRecord
{
    protected static string $resource = PageCategoryResource::class;

    public function getTitle(): string
    {
        return 'Yeni Sayfa Kategorisi Olustur';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Olustur'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'page';

        return $data;
    }
}

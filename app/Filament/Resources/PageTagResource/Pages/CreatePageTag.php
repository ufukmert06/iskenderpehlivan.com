<?php

namespace App\Filament\Resources\PageTagResource\Pages;

use App\Filament\Resources\PageTagResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePageTag extends CreateRecord
{
    protected static string $resource = PageTagResource::class;

    public function getTitle(): string
    {
        return 'Yeni Sayfa Etiketi Olustur';
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

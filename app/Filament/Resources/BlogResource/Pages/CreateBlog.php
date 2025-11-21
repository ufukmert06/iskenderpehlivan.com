<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    public function getTitle(): string
    {
        return 'Yeni Blog Yazisi Olustur';
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
        $data['type'] = 'blog';
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        return $data;
    }
}

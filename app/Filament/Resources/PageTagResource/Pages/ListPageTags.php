<?php

namespace App\Filament\Resources\PageTagResource\Pages;

use App\Filament\Resources\PageTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageTags extends ListRecords
{
    protected static string $resource = PageTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Sayfa Etiketi Olustur'),
        ];
    }
}

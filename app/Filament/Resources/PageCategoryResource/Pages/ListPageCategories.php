<?php

namespace App\Filament\Resources\PageCategoryResource\Pages;

use App\Filament\Resources\PageCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageCategories extends ListRecords
{
    protected static string $resource = PageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Sayfa Kategorisi Olustur'),
        ];
    }
}

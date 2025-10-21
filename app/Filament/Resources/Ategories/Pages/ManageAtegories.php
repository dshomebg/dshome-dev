<?php

namespace App\Filament\Resources\Ategories\Pages;

use App\Filament\Resources\Ategories\AtegoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAtegories extends ManageRecords
{
    protected static string $resource = AtegoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

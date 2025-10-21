<?php

namespace App\Filament\Resources\Roducts\Pages;

use App\Filament\Resources\Roducts\RoductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRoducts extends ManageRecords
{
    protected static string $resource = RoductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

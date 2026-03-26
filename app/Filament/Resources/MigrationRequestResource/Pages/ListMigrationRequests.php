<?php

namespace App\Filament\Resources\MigrationRequestResource\Pages;

use App\Filament\Resources\MigrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMigrationRequests extends ListRecords
{
    protected static string $resource = MigrationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

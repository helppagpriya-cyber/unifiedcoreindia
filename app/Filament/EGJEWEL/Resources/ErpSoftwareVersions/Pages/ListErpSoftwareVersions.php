<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\ErpSoftwareVersionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListErpSoftwareVersions extends ListRecords
{
    protected static string $resource = ErpSoftwareVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

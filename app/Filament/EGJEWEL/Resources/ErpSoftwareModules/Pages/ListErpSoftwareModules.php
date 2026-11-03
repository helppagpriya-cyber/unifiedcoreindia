<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\ErpSoftwareModuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListErpSoftwareModules extends ListRecords
{
    protected static string $resource = ErpSoftwareModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

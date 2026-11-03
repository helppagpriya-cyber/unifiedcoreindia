<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\ErpSoftwareProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListErpSoftwareProducts extends ListRecords
{
    protected static string $resource = ErpSoftwareProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

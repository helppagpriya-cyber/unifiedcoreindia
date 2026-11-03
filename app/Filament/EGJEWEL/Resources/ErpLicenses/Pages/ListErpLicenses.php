<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Pages;

use App\Filament\EGJEWEL\Resources\ErpLicenses\ErpLicenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListErpLicenses extends ListRecords
{
    protected static string $resource = ErpLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

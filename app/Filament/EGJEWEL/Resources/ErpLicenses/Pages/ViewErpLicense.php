<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Pages;

use App\Filament\EGJEWEL\Resources\ErpLicenses\ErpLicenseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewErpLicense extends ViewRecord
{
    protected static string $resource = ErpLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

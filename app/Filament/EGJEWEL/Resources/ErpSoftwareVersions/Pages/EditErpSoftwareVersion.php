<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\ErpSoftwareVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditErpSoftwareVersion extends EditRecord
{
    protected static string $resource = ErpSoftwareVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

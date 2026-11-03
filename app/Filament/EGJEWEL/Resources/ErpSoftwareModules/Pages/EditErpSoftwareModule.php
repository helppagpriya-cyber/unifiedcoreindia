<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\ErpSoftwareModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditErpSoftwareModule extends EditRecord
{
    protected static string $resource = ErpSoftwareModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

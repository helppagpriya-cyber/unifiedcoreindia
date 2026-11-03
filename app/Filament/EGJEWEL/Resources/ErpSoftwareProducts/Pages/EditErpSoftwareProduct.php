<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Pages;

use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\ErpSoftwareProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditErpSoftwareProduct extends EditRecord
{
    protected static string $resource = ErpSoftwareProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

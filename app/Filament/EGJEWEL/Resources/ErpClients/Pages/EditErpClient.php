<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients\Pages;

use App\Filament\EGJEWEL\Resources\ErpClients\ErpClientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditErpClient extends EditRecord
{
    protected static string $resource = ErpClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients\Pages;

use App\Filament\EGJEWEL\Resources\ErpClients\ErpClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListErpClients extends ListRecords
{
    protected static string $resource = ErpClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ClientBranches\Pages;

use App\Filament\Resources\ClientBranches\ClientBranchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientBranches extends ListRecords
{
    protected static string $resource = ClientBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

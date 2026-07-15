<?php

namespace App\Filament\Resources\ClientBranches\Pages;

use App\Filament\Resources\ClientBranches\ClientBranchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClientBranch extends ViewRecord
{
    protected static string $resource = ClientBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

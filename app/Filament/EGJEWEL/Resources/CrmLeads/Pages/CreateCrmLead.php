<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCrmLead extends CreateRecord
{
    protected static string $resource = CrmLeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        if (filled($this->record->assigned_to)) {
            $this->record->assignments()->create([
                'assigned_to' => $this->record->assigned_to,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'remarks' => 'Initial lead assignment',
            ]);
        }
    }
}

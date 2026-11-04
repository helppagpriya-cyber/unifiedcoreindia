<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCrmLead extends EditRecord
{
    protected static string $resource = CrmLeadResource::class;

    protected ?int $previousAssignedTo = null;

    protected function beforeSave(): void
    {
        $this->previousAssignedTo = $this->record->assigned_to
            ? (int) $this->record->assigned_to
            : null;
    }

    protected function afterSave(): void
    {
        $this->record->refresh();

        $currentAssignedTo = $this->record->assigned_to
            ? (int) $this->record->assigned_to
            : null;

        if ($this->previousAssignedTo !== $currentAssignedTo) {
            $this->record->assignments()->create([
                'assigned_to' => $currentAssignedTo,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'remarks' => $currentAssignedTo
                    ? 'Lead reassigned'
                    : 'Lead unassigned',
            ]);
        }
    }
}

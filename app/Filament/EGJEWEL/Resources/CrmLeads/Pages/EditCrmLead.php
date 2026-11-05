<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCrmLead extends EditRecord
{
    protected static string $resource = CrmLeadResource::class;

    protected ?int $previousAssignedTo = null;

    protected ?int $previousStatusId = null;

    protected ?string $previousStatusName = null;

    protected function beforeSave(): void
    {
        $this->previousAssignedTo = $this->record->assigned_to
            ? (int) $this->record->assigned_to
            : null;
        $this->previousStatusId = $this->record->status_id
            ? (int) $this->record->status_id
            : null;
        $this->previousStatusName = $this->record->status?->name ?? 'Unspecified';
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

        $currentStatusId = $this->record->status_id
            ? (int) $this->record->status_id
            : null;

        if ($this->previousStatusId !== $currentStatusId) {
            $currentStatus = $this->record->status;

            $this->record->activities()->create([
                'user_id' => Auth::id(),
                'activity_type' => 'note',
                'subject' => 'Lead status changed',
                'description' => sprintf(
                    'Status changed from %s to %s.',
                    $this->previousStatusName ?? 'Unspecified',
                    $currentStatus?->name ?? 'Unspecified',
                ),
                'outcome' => $currentStatus?->is_lost ? $this->record->lost_reason : null,
                'activity_at' => now(),
            ]);
        }
    }
}

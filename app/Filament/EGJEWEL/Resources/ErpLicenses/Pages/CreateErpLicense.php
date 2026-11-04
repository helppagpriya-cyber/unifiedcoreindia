<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Pages;

use App\Filament\EGJEWEL\Resources\ErpLicenses\ErpLicenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateErpLicense extends CreateRecord
{
    protected static string $resource = ErpLicenseResource::class;

    protected function afterCreate(): void
    {
        $recordId = $this->record->getKey();

        $year = $this->record->created_at?->format('Y')
            ?? now()->format('Y');

        $licenseNumber = 'EG' . $year . '-' . str_pad(
            (string) $recordId,
            5,
            '0',
            STR_PAD_LEFT
        );

        $this->record
            ->forceFill([
                'license_number' => $licenseNumber,
            ])
            ->saveQuietly();

        $this->record->refresh();
    }
}

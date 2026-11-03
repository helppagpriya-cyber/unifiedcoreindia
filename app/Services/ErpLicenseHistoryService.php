<?php

namespace App\Services;

use App\Models\ErpLicense;
use App\Models\ErpLicenseHistory;
use Illuminate\Support\Facades\Auth;

class ErpLicenseHistoryService
{
    /**
     * Capture the complete licence configuration before a change.
     */
    public function capture(ErpLicense $license): array
    {
        $license->load([
            'client',
            'branch',
            'softwareProduct',
            'softwareVersion',
            'modules',
        ]);

        return [
            'client' => [
                'id' => $license->erp_client_id,
                'company_name' => $license->client?->company_name,
                'client_code' => $license->client?->client_code,
            ],

            'branch' => [
                'id' => $license->erp_client_branch_id,
                'branch_name' => $license->branch?->branch_name,
                'branch_code' => $license->branch?->branch_code,
            ],

            'software_product' => [
                'id' => $license->software_product_id,
                'name' => $license->softwareProduct?->name,
                'code' => $license->softwareProduct?->code,
            ],

            'software_version' => [
                'id' => $license->software_version_id,
                'version_name' => $license->softwareVersion?->version_name,
                'legacy_edition_number' => $license->softwareVersion?->legacy_edition_number,
                'legacy_version_code' => $license->softwareVersion?->legacy_version_code,
            ],

            'license_type' => $license->license_type,
            'status' => $license->status,

            'installation_date' => $license->installation_date?->toDateString(),
            'amc_expiry_date' => $license->amc_expiry_date?->toDateString(),
            'demo_expiry_date' => $license->demo_expiry_date?->toDateString(),

            'motherboard_serial' => $license->motherboard_serial,

            'lan_enabled' => $license->lan_enabled,
            'gst_enabled' => $license->gst_enabled,

            'sales_points' => $license->sales_points,
            'max_companies' => $license->max_companies,
            'max_simultaneous_users' => $license->max_simultaneous_users,
            'customer_code' => $license->customer_code,

            'modules' => $license->modules
                ->map(fn($module) => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'legacy_flag_key' => $module->legacy_flag_key,
                    'legacy_group' => $module->legacy_group,
                    'legacy_position' => $module->legacy_position,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Record a completed licence operation.
     *
     * Call this after updating the licence, passing the
     * configuration captured before the update.
     */
    public function recordChange(
        ErpLicense $license,
        string $operationType,
        array $previousConfiguration = [],
        ?string $previousLicenseKey = null,
        ?string $remarks = null
    ): ErpLicenseHistory {
        $license->refresh();

        $newConfiguration = $this->capture($license);

        return $license->histories()->create([
            'performed_by' => Auth::id(),
            'operation_type' => $operationType,

            'previous_configuration' => $previousConfiguration,
            'new_configuration' => $newConfiguration,

            'previous_license_key' => $previousLicenseKey,
            'new_license_key' => $license->license_key,

            'previous_installation_date' =>
            $previousConfiguration['installation_date'] ?? null,

            'new_installation_date' =>
            $license->installation_date?->toDateString(),

            'previous_amc_expiry_date' =>
            $previousConfiguration['amc_expiry_date'] ?? null,

            'new_amc_expiry_date' =>
            $license->amc_expiry_date?->toDateString(),

            'remarks' => $remarks,
        ]);
    }
}

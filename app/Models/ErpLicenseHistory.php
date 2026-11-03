<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpLicenseHistory extends Model
{
    protected $fillable = [
        'erp_license_id',
        'performed_by',
        'operation_type',
        'previous_configuration',
        'new_configuration',
        'previous_license_key',
        'new_license_key',
        'previous_installation_date',
        'new_installation_date',
        'previous_amc_expiry_date',
        'new_amc_expiry_date',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'previous_configuration' => 'array',
            'new_configuration' => 'array',
            'previous_installation_date' => 'date',
            'new_installation_date' => 'date',
            'previous_amc_expiry_date' => 'date',
            'new_amc_expiry_date' => 'date',
        ];
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(ErpLicense::class, 'erp_license_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

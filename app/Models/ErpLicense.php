<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpLicense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'license_number',
        'erp_client_id',
        'erp_client_branch_id',
        'software_product_id',
        'software_version_id',
        'license_type',
        'status',
        'issued_at',
        'demo_expiry_date',
        'installation_date',
        'amc_expiry_date',
        'activated_at',
        'motherboard_serial',
        'lan_enabled',
        'gst_enabled',
        'sales_points',
        'max_companies',
        'max_simultaneous_users',
        'customer_code',
        'license_key',
        'legacy_serial_number',
        'legacy_demo_id',
        'legacy_software_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'demo_expiry_date' => 'date',
            'installation_date' => 'date',
            'amc_expiry_date' => 'date',
            'activated_at' => 'datetime',
            'lan_enabled' => 'boolean',
            'gst_enabled' => 'boolean',
            'legacy_demo_id' => 'integer',
            'legacy_software_id' => 'string',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ErpClient::class, 'erp_client_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ErpClientBranch::class, 'erp_client_branch_id');
    }

    public function softwareProduct(): BelongsTo
    {
        return $this->belongsTo(
            ErpSoftwareProduct::class,
            'software_product_id'
        );
    }

    public function softwareVersion(): BelongsTo
    {
        return $this->belongsTo(
            ErpSoftwareVersion::class,
            'software_version_id'
        );
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            ErpSoftwareModule::class,
            'erp_license_modules',
            'erp_license_id',
            'erp_software_module_id'
        )->withTimestamps();
    }
    public function histories(): HasMany
    {
        return $this->hasMany(ErpLicenseHistory::class);
    }
}

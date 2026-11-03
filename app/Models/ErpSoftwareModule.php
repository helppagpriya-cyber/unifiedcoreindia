<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ErpSoftwareModule extends Model
{
    protected $fillable = [
        'software_product_id',
        'name',
        'code',
        'legacy_flag_key',
        'legacy_group',
        'legacy_position',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function softwareProduct(): BelongsTo
    {
        return $this->belongsTo(ErpSoftwareProduct::class, 'software_product_id');
    }
    public function licenses(): BelongsToMany
    {
        return $this->belongsToMany(
            ErpLicense::class,
            'erp_license_modules',
            'erp_software_module_id',
            'erp_license_id'
        )->withTimestamps();
    }
}

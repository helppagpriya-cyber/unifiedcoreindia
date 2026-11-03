<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpSoftwareVersion extends Model
{
    protected $fillable = [
        'software_product_id',
        'version_name',
        'legacy_version_name',
        'legacy_edition_number',
        'legacy_version_code',
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
    public function licenses(): HasMany
    {
        return $this->hasMany(ErpLicense::class, 'software_version_id');
    }
}

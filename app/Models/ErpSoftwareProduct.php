<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpSoftwareProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'legacy_product_code',
        'software_id_format',
        'license_profile',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'software_id_format' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ErpSoftwareVersion::class, 'software_product_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ErpSoftwareModule::class, 'software_product_id');
    }


    public function licenses(): HasMany
    {
        return $this->hasMany(ErpLicense::class, 'software_product_id');
    }
}

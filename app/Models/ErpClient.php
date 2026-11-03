<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_code',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'alternate_phone',
        'gst_number',
        'pan_number',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(ErpClientBranch::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(ErpLicense::class);
    }
}

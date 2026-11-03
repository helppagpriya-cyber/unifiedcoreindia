<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpClientBranch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'erp_client_id',
        'branch_code',
        'branch_name',
        'is_head_office',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_head_office' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ErpClient::class, 'erp_client_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(ErpLicense::class);
    }
}

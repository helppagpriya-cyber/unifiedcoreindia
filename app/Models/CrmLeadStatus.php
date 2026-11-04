<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLeadStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_final',
        'is_won',
        'is_lost',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(CrmLead::class, 'status_id');
    }
}

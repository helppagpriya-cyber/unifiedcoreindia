<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_number',
        'lead_date',

        'company_name',
        'business_type',

        'address',
        'city',
        'state',
        'pincode',
        'country',

        'gst_number',
        'pan_number',

        'contact_person',
        'mobile',
        'alternate_mobile',
        'whatsapp_number',
        'email',

        'product_interested',
        'existing_software',
        'requirements',
        'estimated_budget',

        'source_id',
        'status_id',
        'priority',

        'assigned_to',
        'created_by',

        'next_follow_up_at',

        'converted_erp_client_id',
        'converted_at',

        'lost_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'lead_date' => 'date',
            'estimated_budget' => 'decimal:2',
            'next_follow_up_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Automatic Lead Number
    |--------------------------------------------------------------------------
    |
    | Example: LEAD-2026-00001
    |
    */
    protected static function booted(): void
    {
        static::created(function (CrmLead $lead) {
            if (blank($lead->lead_number)) {
                $year = $lead->lead_date?->format('Y')
                    ?? $lead->created_at->format('Y');

                $lead->forceFill([
                    'lead_number' => 'LEAD-' . $year . '-' .
                        str_pad(
                            (string) $lead->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),
                ])->saveQuietly();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmLeadSource::class, 'source_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CrmLeadStatus::class, 'status_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmLeadActivity::class, 'crm_lead_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CrmLeadAssignment::class, 'crm_lead_id')
            ->latest('assigned_at');
    }

    public function convertedErpClient(): BelongsTo
    {
        return $this->belongsTo(
            ErpClient::class,
            'converted_erp_client_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePendingFollowUps($query)
    {
        return $query->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->whereNull('converted_at');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}

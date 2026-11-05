<?php

namespace App\Services\CRM;

use App\Models\CrmLead;
use App\Models\CrmLeadStatus;
use App\Models\ErpClient;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ConvertCrmLeadToErpClient
{
    public function convert(CrmLead $lead): ErpClient
    {
        return DB::transaction(function () use ($lead): ErpClient {
            $lead = CrmLead::query()->lockForUpdate()->findOrFail($lead->id);

            if ($lead->converted_erp_client_id) {
                return $lead->convertedErpClient()->firstOrFail();
            }

            $wonStatus = CrmLeadStatus::query()->where('is_won', true)->where('is_active', true)->first();

            if (! $wonStatus) {
                throw new RuntimeException('No active Won lead status is configured.');
            }

            $client = ErpClient::create([
                'client_code' => 'CRM-' . $lead->id,
                'company_name' => $lead->company_name,
                'contact_person' => $lead->contact_person,
                'email' => $lead->email,
                'phone' => $lead->mobile,
                'alternate_phone' => $lead->alternate_mobile,
                'gst_number' => $lead->gst_number,
                'pan_number' => $lead->pan_number,
                'address' => $lead->address,
                'city' => $lead->city,
                'state' => $lead->state,
                'pincode' => $lead->pincode,
                'country' => $lead->country ?: 'India',
                'notes' => trim(implode("\n\n", array_filter([
                    'Converted from CRM lead ' . ($lead->lead_number ?: '#' . $lead->id),
                    $lead->requirements,
                    $lead->notes,
                ]))),
                'is_active' => true,
            ]);

            $lead->update([
                'converted_erp_client_id' => $client->id,
                'converted_at' => now(),
                'status_id' => $wonStatus->id,
                'next_follow_up_at' => null,
            ]);

            $lead->activities()->create([
                'user_id' => auth()->id(),
                'activity_type' => 'note',
                'subject' => 'Converted to ERP client',
                'description' => "Created ERP client {$client->client_code}.",
                'activity_at' => now(),
            ]);

            return $client;
        });
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CrmLeadImportTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        // Keep the template empty; users enter their own lead records.
        return [];
    }

    public function headings(): array
    {
        return [
            'company_name',
            'contact_person',
            'mobile',
            'alternate_mobile',
            'whatsapp_number',
            'email',
            'business_type',
            'address',
            'city',
            'state',
            'pincode',
            'country',
            'gst_number',
            'pan_number',
            'product_interested',
            'existing_software',
            'requirements',
            'estimated_budget',
            'source',
            'status',
            'priority',
            'assigned_to_email',
            'lead_date',
            'next_follow_up_at',
        ];
    }

    public function title(): string
    {
        return 'CRM Lead Import';
    }
}

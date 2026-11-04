<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;

class CrmLeadSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Lead Sources
        |--------------------------------------------------------------------------
        */
        $sources = [
            ['name' => 'Exhibition', 'slug' => 'exhibition'],
            ['name' => 'Direct Calling', 'slug' => 'direct-calling'],
            ['name' => 'Website', 'slug' => 'website'],
            ['name' => 'Referral', 'slug' => 'referral'],
            ['name' => 'Social Media', 'slug' => 'social-media'],
            ['name' => 'Email', 'slug' => 'email'],
            ['name' => 'Existing Customer', 'slug' => 'existing-customer'],
            ['name' => 'Other', 'slug' => 'other'],
        ];

        foreach ($sources as $index => $source) {
            CrmLeadSource::updateOrCreate(
                ['slug' => $source['slug']],
                [
                    'name' => $source['name'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lead Statuses
        |--------------------------------------------------------------------------
        */
        $statuses = [
            [
                'name' => 'New',
                'slug' => 'new',
                'color' => 'info',
            ],
            [
                'name' => 'Contacted',
                'slug' => 'contacted',
                'color' => 'primary',
            ],
            [
                'name' => 'Interested',
                'slug' => 'interested',
                'color' => 'success',
            ],
            [
                'name' => 'Demo Scheduled',
                'slug' => 'demo-scheduled',
                'color' => 'warning',
            ],
            [
                'name' => 'Demo Completed',
                'slug' => 'demo-completed',
                'color' => 'info',
            ],
            [
                'name' => 'Negotiation',
                'slug' => 'negotiation',
                'color' => 'warning',
            ],
            [
                'name' => 'Converted',
                'slug' => 'converted',
                'color' => 'success',
                'is_final' => true,
                'is_won' => true,
            ],
            [
                'name' => 'Lost',
                'slug' => 'lost',
                'color' => 'danger',
                'is_final' => true,
                'is_lost' => true,
            ],
            [
                'name' => 'On Hold',
                'slug' => 'on-hold',
                'color' => 'gray',
            ],
        ];

        foreach ($statuses as $index => $status) {
            CrmLeadStatus::updateOrCreate(
                ['slug' => $status['slug']],
                [
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'is_final' => $status['is_final'] ?? false,
                    'is_won' => $status['is_won'] ?? false,
                    'is_lost' => $status['is_lost'] ?? false,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}

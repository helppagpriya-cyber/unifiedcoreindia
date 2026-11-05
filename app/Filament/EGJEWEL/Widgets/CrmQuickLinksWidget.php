<?php

namespace App\Filament\EGJEWEL\Widgets;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use App\Filament\EGJEWEL\Resources\ErpClients\ErpClientResource;
use App\Filament\EGJEWEL\Resources\ErpLicenses\ErpLicenseResource;
use App\Models\CrmLead;
use App\Models\CrmLeadStatus;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;

class CrmQuickLinksWidget extends Widget
{
    protected string $view = 'filament.egjewel.widgets.crm-quick-links-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $myLeads = CrmLead::query()->where('assigned_to', $user?->id);
        $openLeads = (clone $myLeads)
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn ($query) => $query->where('is_final', true));

        $dueFollowUps = (clone $myLeads)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn ($query) => $query->where('is_final', true));

        $metrics = [
            [
                'label' => 'Follow-ups due',
                'value' => $dueFollowUps->count(),
                'hint' => 'Calls and tasks to complete',
                'icon' => 'clock',
                'tone' => 'amber',
                'url' => CrmLeadResource::getUrl('index', ['tab' => 'due']),
            ],
            [
                'label' => 'My active leads',
                'value' => $openLeads->count(),
                'hint' => 'Your current sales pipeline',
                'icon' => 'user-group',
                'tone' => 'sky',
                'url' => CrmLeadResource::getUrl('index', ['tab' => 'my-open']),
            ],
            [
                'label' => 'Unassigned leads',
                'value' => CrmLead::query()
                    ->whereNull('assigned_to')
                    ->whereNull('converted_at')
                    ->whereDoesntHave('status', fn ($query) => $query->where('is_final', true))
                    ->count(),
                'hint' => 'New enquiries awaiting an owner',
                'icon' => 'inbox',
                'tone' => 'violet',
                'url' => CrmLeadResource::getUrl('index', ['tab' => 'unassigned']),
            ],
            [
                'label' => 'High priority',
                'value' => (clone $openLeads)->whereIn('priority', ['high', 'urgent'])->count(),
                'hint' => 'Leads needing extra attention',
                'icon' => 'bolt',
                'tone' => 'rose',
                'url' => CrmLeadResource::getUrl('index', ['tab' => 'high-priority']),
            ],
            [
                'label' => 'Won this month',
                'value' => CrmLead::query()
                    ->where('assigned_to', $user?->id)
                    ->whereBetween('converted_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'hint' => 'Converted to ERP clients',
                'icon' => 'check',
                'tone' => 'emerald',
                'url' => CrmLeadResource::getUrl('index', ['tab' => 'won-month']),
            ],
        ];

        $followUps = (clone $dueFollowUps)
            ->with('status')
            ->orderBy('next_follow_up_at')
            ->limit(6)
            ->get();

        $pipeline = CrmLeadStatus::query()
            ->where('is_active', true)
            ->where('is_final', false)
            ->orderBy('sort_order')
            ->withCount(['leads' => fn ($query) => $query
                ->where('assigned_to', $user?->id)
                ->whereNull('converted_at')])
            ->get();

        $pipelineTotal = max(1, $pipeline->sum('leads_count'));

        return [
            'firstName' => str($user?->name ?? 'there')->before(' ')->toString(),
            'todayLabel' => now()->format('l, j F Y'),
            'metrics' => $metrics,
            'followUps' => $followUps,
            'pipeline' => $pipeline,
            'pipelineTotal' => $pipelineTotal,
            'quickLinks' => [
                ['title' => 'Create a lead', 'description' => 'Capture a new enquiry', 'icon' => 'plus', 'url' => CrmLeadResource::getUrl('create'), 'tone' => 'indigo'],
                ['title' => 'Open lead inbox', 'description' => 'Search and update leads', 'icon' => 'inbox', 'url' => CrmLeadResource::getUrl('index'), 'tone' => 'sky'],
                ['title' => 'Add ERP client', 'description' => 'Register a customer', 'icon' => 'building', 'url' => ErpClientResource::getUrl('create'), 'tone' => 'emerald'],
                ['title' => 'Manage licenses', 'description' => 'Issue or update licenses', 'icon' => 'key', 'url' => ErpLicenseResource::getUrl('index'), 'tone' => 'violet'],
            ],
        ];
    }
}

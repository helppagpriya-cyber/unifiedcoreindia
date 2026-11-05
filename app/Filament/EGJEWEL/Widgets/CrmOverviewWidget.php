<?php

namespace App\Filament\EGJEWEL\Widgets;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use App\Models\CrmLead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();
        $myLeads = CrmLead::query()->where('assigned_to', $userId);
        $dueFollowUps = (clone $myLeads)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn ($query) => $query->where('is_final', true));

        return [
            Stat::make('My open leads', (clone $myLeads)
                ->whereNull('converted_at')
                ->whereDoesntHave('status', fn ($query) => $query->where('is_final', true))
                ->count())
                ->description('Assigned to you')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->url(CrmLeadResource::getUrl('index', ['tab' => 'my-open'])),

            Stat::make('Follow-ups due', $dueFollowUps->count())
                ->description('Needs attention today')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url(CrmLeadResource::getUrl('index', ['tab' => 'due'])),

            Stat::make('Unassigned leads', CrmLead::query()->whereNull('assigned_to')->count())
                ->description('Ready to be assigned')
                ->icon('heroicon-o-inbox')
                ->color('info')
                ->url(CrmLeadResource::getUrl('index', ['tab' => 'unassigned'])),

            Stat::make('Converted this month', CrmLead::query()
                ->whereNotNull('converted_at')
                ->whereBetween('converted_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count())
                ->description('Leads converted to ERP clients')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->url(CrmLeadResource::getUrl('index', ['tab' => 'won-month'])),
        ];
    }
}

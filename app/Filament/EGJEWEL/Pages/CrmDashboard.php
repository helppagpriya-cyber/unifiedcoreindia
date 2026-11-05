<?php

namespace App\Filament\EGJEWEL\Pages;

use App\Filament\EGJEWEL\Widgets\CrmQuickLinksWidget;
use Filament\Pages\Dashboard;
use UnitEnum;

class CrmDashboard extends Dashboard
{
    protected static ?string $title = 'Sales Dashboard';

    protected static ?string $navigationLabel = 'Sales Dashboard';

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            CrmQuickLinksWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}

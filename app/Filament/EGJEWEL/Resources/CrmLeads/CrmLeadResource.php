<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads;

use App\Filament\EGJEWEL\Resources\CrmLeads\Pages\CreateCrmLead;
use App\Filament\EGJEWEL\Resources\CrmLeads\Pages\EditCrmLead;
use App\Filament\EGJEWEL\Resources\CrmLeads\Pages\ListCrmLeads;
use App\Filament\EGJEWEL\Resources\CrmLeads\Schemas\CrmLeadForm;
use App\Filament\EGJEWEL\Resources\CrmLeads\Tables\CrmLeadsTable;

use App\Filament\EGJEWEL\Resources\CrmLeads\RelationManagers\ActivitiesRelationManager;
use App\Models\CrmLead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CrmLeadResource extends Resource
{
    protected static ?string $model = CrmLead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Leads';

    protected static string|UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CrmLeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmLeadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmLeads::route('/'),
            'create' => CreateCrmLead::route('/create'),
            'edit' => EditCrmLead::route('/{record}/edit'),

        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

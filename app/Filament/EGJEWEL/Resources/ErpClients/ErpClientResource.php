<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients;

use App\Filament\EGJEWEL\Resources\ErpClients\Pages\CreateErpClient;
use App\Filament\EGJEWEL\Resources\ErpClients\Pages\EditErpClient;
use App\Filament\EGJEWEL\Resources\ErpClients\Pages\ListErpClients;
use App\Filament\EGJEWEL\Resources\ErpClients\Schemas\ErpClientForm;
use App\Filament\EGJEWEL\Resources\ErpClients\Tables\ErpClientsTable;
use App\Filament\Resources\Clients\RelationManagers\BranchesRelationManager;

use App\Models\ErpClient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ErpClientResource extends Resource
{
    protected static ?string $model = ErpClient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Clients';

    protected static string|UnitEnum|null $navigationGroup = 'ERP Operations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ErpClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpClients::route('/'),
            'create' => CreateErpClient::route('/create'),
            'edit' => EditErpClient::route('/{record}/edit'),
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

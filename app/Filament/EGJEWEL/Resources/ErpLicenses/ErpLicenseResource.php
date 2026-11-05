<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses;

use App\Filament\EGJEWEL\Resources\ErpLicenses\Pages\CreateErpLicense;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Pages\EditErpLicense;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Pages\ListErpLicenses;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Pages\ViewErpLicense;
use App\Filament\EGJEWEL\Resources\ErpLicenses\RelationManagers\HistoriesRelationManager;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Schemas\ErpLicenseForm;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Schemas\ErpLicenseInfolist;
use App\Filament\EGJEWEL\Resources\ErpLicenses\Tables\ErpLicensesTable;
use App\Models\ErpLicense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ErpLicenseResource extends Resource
{
    protected static ?string $model = ErpLicense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Licenses';

    protected static string|UnitEnum|null $navigationGroup = 'ERP Operations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ErpLicenseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ErpLicenseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpLicensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpLicenses::route('/'),
            'create' => CreateErpLicense::route('/create'),
            'view' => ViewErpLicense::route('/{record}'),
            'edit' => EditErpLicense::route('/{record}/edit'),
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

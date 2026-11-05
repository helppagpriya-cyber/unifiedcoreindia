<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareProducts;

use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Pages\CreateErpSoftwareProduct;
use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Pages\EditErpSoftwareProduct;
use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Pages\ListErpSoftwareProducts;
use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Schemas\ErpSoftwareProductForm;
use App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Tables\ErpSoftwareProductsTable;
use App\Models\ErpSoftwareProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ErpSoftwareProductResource extends Resource
{
    protected static ?string $model = ErpSoftwareProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Products';

    protected static string|UnitEnum|null $navigationGroup = 'Software Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ErpSoftwareProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpSoftwareProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpSoftwareProducts::route('/'),
            'create' => CreateErpSoftwareProduct::route('/create'),
            'edit' => EditErpSoftwareProduct::route('/{record}/edit'),
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

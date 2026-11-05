<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareVersions;

use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Pages\CreateErpSoftwareVersion;
use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Pages\EditErpSoftwareVersion;
use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Pages\ListErpSoftwareVersions;
use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Schemas\ErpSoftwareVersionForm;
use App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Tables\ErpSoftwareVersionsTable;
use App\Models\ErpSoftwareVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ErpSoftwareVersionResource extends Resource
{
    protected static ?string $model = ErpSoftwareVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Versions & Editions';

    protected static string|UnitEnum|null $navigationGroup = 'Software Catalog';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ErpSoftwareVersionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpSoftwareVersionsTable::configure($table);
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
            'index' => ListErpSoftwareVersions::route('/'),
            'create' => CreateErpSoftwareVersion::route('/create'),
            'edit' => EditErpSoftwareVersion::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules;

use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Pages\CreateErpSoftwareModule;
use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Pages\EditErpSoftwareModule;
use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Pages\ListErpSoftwareModules;
use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Schemas\ErpSoftwareModuleForm;
use App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Tables\ErpSoftwareModulesTable;
use App\Models\ErpSoftwareModule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ErpSoftwareModuleResource extends Resource
{
    protected static ?string $model = ErpSoftwareModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ErpSoftwareModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpSoftwareModulesTable::configure($table);
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
            'index' => ListErpSoftwareModules::route('/'),
            'create' => CreateErpSoftwareModule::route('/create'),
            'edit' => EditErpSoftwareModule::route('/{record}/edit'),
        ];
    }
}

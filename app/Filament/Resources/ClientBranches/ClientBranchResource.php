<?php

namespace App\Filament\Resources\ClientBranches;

use App\Filament\Resources\ClientBranches\Pages\CreateClientBranch;
use App\Filament\Resources\ClientBranches\Pages\EditClientBranch;
use App\Filament\Resources\ClientBranches\Pages\ListClientBranches;
use App\Filament\Resources\ClientBranches\Pages\ViewClientBranch;
use App\Filament\Resources\ClientBranches\Schemas\ClientBranchForm;
use App\Filament\Resources\ClientBranches\Schemas\ClientBranchInfolist;
use App\Filament\Resources\ClientBranches\Tables\ClientBranchesTable;
use App\Models\ClientBranch;
use App\Traits\HasResourcePermissions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientBranchResource extends Resource
{
    use HasResourcePermissions;

    protected static function permissionPrefix(): string
    {
        return 'client_branch';
    }
    protected static ?string $model = ClientBranch::class;


    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;
    public static function getNavigationGroup(): ?string
    {
        return 'Masters';
    }

    public static function form(Schema $schema): Schema
    {
        return ClientBranchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientBranchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientBranchesTable::configure($table);
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
            'index' => ListClientBranches::route('/'),
            'create' => CreateClientBranch::route('/create'),
            'view' => ViewClientBranch::route('/{record}'),
            'edit' => EditClientBranch::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'branch_code',
            'branch_name',
            'city',
            'client.company_name',
        ];
    }
}

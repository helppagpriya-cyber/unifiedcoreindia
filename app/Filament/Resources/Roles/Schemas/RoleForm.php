<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Details')
                    ->schema([

                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true),

                    ]),

                Section::make('Permissions')
                    ->schema([

                        CheckboxList::make('permissions')
                            ->options(function () {

                                return Permission::all()
                                    ->pluck('name', 'id');
                            })
                            ->relationship('permissions', 'name')
                            ->columns(4)
                            ->searchable()
                            ->bulkToggleable(),

                    ]),
            ])->columns(1);
    }
}

<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Details')
                    ->schema([

                        TextEntry::make('name')
                            ->label('Role Name'),

                    ]),
                Section::make('Role Statistics')
                    ->schema([
                        TextEntry::make('permissions_count')
                            ->label('Permissions Count')
                            ->counts('permissions'),
                        TextEntry::make('users_count')
                            ->label('Users Count')
                            ->counts('users'),
                    ]),
            ]);
    }
}

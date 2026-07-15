<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission Information')

                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),


                        TextEntry::make('name')
                            ->label('Name'),


                        TextEntry::make('guard_name')
                            ->label('Guard Name'),
                    ])->columns(3),
            ]);
    }
}

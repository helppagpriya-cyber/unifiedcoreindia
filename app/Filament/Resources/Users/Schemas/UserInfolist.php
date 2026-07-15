<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Information')
                    ->schema([

                        TextEntry::make('employee_code')
                            ->label('User Code'),

                        TextEntry::make('name'),

                        TextEntry::make('email'),

                        TextEntry::make('mobile'),
                        TextEntry::make('designation'),
                        TextEntry::make('joining_date')
                            ->date(),

                        TextEntry::make('roles.name')
                            ->label('Roles')
                            ->badge(),

                    ])
                    ->columns(2),



                Section::make('Status')
                    ->schema([
                        TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ])
                    ->columns(2),
            ])->columns(1);
    }
}

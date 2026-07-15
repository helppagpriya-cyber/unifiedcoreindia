<?php

namespace App\Filament\Resources\Permissions\Tables;


use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')

                    ->label('Permission Name')
                    ->searchable(),

                TextColumn::make('guard_name'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),

            ])
            ->toolbarActions([
                //
            ]);
    }
}

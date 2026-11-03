<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ErpSoftwareModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('softwareProduct.name')
                    ->label('Software')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable(),

                TextColumn::make('legacy_flag_key')
                    ->label('Legacy Flag'),

                TextColumn::make('legacy_group')
                    ->label('Group'),

                TextColumn::make('legacy_position')
                    ->label('Position'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

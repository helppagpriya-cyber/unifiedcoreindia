<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
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

                TextColumn::make('package_original_name')
                    ->label('ZIP Package')
                    ->placeholder('Not uploaded')
                    ->limit(32),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('download')
                    ->label('Download ZIP')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->url(fn ($record): string => route('erp-packages.module.download', $record))
                    ->visible(fn ($record): bool => filled($record->package_path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

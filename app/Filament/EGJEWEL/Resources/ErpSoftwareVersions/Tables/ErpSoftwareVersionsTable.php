<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ErpSoftwareVersionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('softwareProduct.name')
                    ->label('Software')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('version_name')
                    ->label('Version')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('legacy_version_name')
                    ->label('Legacy Name'),

                TextColumn::make('legacy_version_code')
                    ->label('VFP Code'),

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
                    ->url(fn ($record): string => route('erp-packages.version.download', $record))
                    ->visible(fn ($record): bool => filled($record->package_path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

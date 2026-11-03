<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ErpSoftwareProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('legacy_product_code')
                    ->label('Legacy Code')
                    ->placeholder('Not configured'),

                TextColumn::make('versions_count')
                    ->counts('versions')
                    ->label('Versions'),

                TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label('Modules'),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

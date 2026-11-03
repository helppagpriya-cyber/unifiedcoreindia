<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ErpLicensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('license_number')
                    ->label('Licence No.')
                    ->searchable()
                    ->placeholder('Not assigned'),

                TextColumn::make('client.company_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->placeholder('Main Client'),

                TextColumn::make('softwareProduct.name')
                    ->label('Software')
                    ->sortable(),

                TextColumn::make('softwareVersion.version_name')
                    ->label('Edition')
                    ->wrap(),

                TextColumn::make('license_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'demo' => 'warning',
                        'full' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'issued' => 'info',
                        'active' => 'success',
                        'expired' => 'danger',
                        'suspended' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('amc_expiry_date')
                    ->label('AMC Expiry')
                    ->date('d M Y')
                    ->placeholder('Not set')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}

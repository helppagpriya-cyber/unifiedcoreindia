<?php

namespace App\Filament\Resources\ClientBranches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Tables\Table;

class ClientBranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch_code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.company_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->searchable(),

                TextColumn::make('state')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->date('d-M-Y'),
            ])

            ->filters([
                SelectFilter::make('client_id')
                    ->relationship('client', 'company_name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active'),

            ])
            ->recordActions([
                ViewAction::make(),
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

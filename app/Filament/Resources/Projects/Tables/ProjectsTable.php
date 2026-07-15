<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\BillingType;
use App\Enums\ProjectStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project_code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project_name')
                    ->searchable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->searchable(),

                TextColumn::make('branch.client.company_name')
                    ->label('Client'),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('billing_type')
                    ->badge(),

                TextColumn::make('contract_value')
                    ->money('INR'),

                TextColumn::make('created_at')
                    ->date('d-M-Y'),
            ])
            ->filters([
                SelectFilter::make('client')
                    ->relationship(
                        'branch.client',
                        'company_name'
                    ),
                SelectFilter::make('client_branch_id')
                    ->relationship(
                        'branch',
                        'branch_name'
                    ),
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),
                SelectFilter::make('billing_type')
                    ->options(BillingType::class),
                TrashedFilter::make(),
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

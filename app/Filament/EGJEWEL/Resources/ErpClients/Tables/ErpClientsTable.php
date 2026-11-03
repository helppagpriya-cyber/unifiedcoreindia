<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ErpClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_code')
                    ->label('Client Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('contact_person')
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('branches_count')
                    ->label('Branches')
                    ->counts('branches')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Client Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->placeholder('All clients'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

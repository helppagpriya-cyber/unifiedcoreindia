<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lead_number')
                    ->label('Lead No.')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn($record): ?string =>
                        $record->contact_person
                            ? 'Contact: ' . $record->contact_person
                            : null
                    )
                    ->wrap(),

                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Not provided'),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('source.name')
                    ->label('Source')
                    ->placeholder('Not specified'),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn($record): string =>
                        $record->status?->color ?? 'gray'
                    ),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned Executive')
                    ->placeholder('Unassigned')
                    ->searchable(),

                TextColumn::make('next_follow_up_at')
                    ->label('Next Follow-up')
                    ->dateTime('d M Y, h:i A')
                    ->placeholder('Not scheduled')
                    ->sortable(),

                TextColumn::make('lead_date')
                    ->label('Lead Date')
                    ->date('d M Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status_id')
                    ->label('Lead Status')
                    ->relationship('status', 'name'),

                SelectFilter::make('source_id')
                    ->label('Lead Source')
                    ->relationship('source', 'name'),

                SelectFilter::make('assigned_to')
                    ->label('Assigned Executive')
                    ->relationship('assignedTo', 'name'),

                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Filter::make('pending_follow_up')
                    ->label('Follow-up Due')
                    ->query(
                        fn(Builder $query): Builder => $query
                            ->whereNotNull('next_follow_up_at')
                            ->where('next_follow_up_at', '<=', now())
                            ->whereNull('converted_at')
                    ),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                EditAction::make(),
            ]);
    }
}

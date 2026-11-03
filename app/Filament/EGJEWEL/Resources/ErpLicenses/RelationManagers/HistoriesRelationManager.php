<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $title = 'Licence History';

    protected static ?string $recordTitleAttribute = 'operation_type';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('operation_type')
                    ->label('Operation')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'demo_created' => 'warning',
                        'installation' => 'info',
                        'activation' => 'success',
                        'upgrade' => 'primary',
                        'amc_renewal' => 'success',
                        'suspension' => 'warning',
                        'cancellation' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('performedBy.name')
                    ->label('Performed By')
                    ->placeholder('System'),

                TextColumn::make('previous_amc_expiry_date')
                    ->label('Previous AMC')
                    ->date('d M Y')
                    ->placeholder('—'),

                TextColumn::make('new_amc_expiry_date')
                    ->label('New AMC')
                    ->date('d M Y')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Operation Date')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                TextColumn::make('remarks')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('View Details')
                    ->modalHeading('Licence History Details')
                    ->schema([
                        Section::make('Operation Information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('operation_type')
                                    ->disabled(),

                                TextInput::make('performedBy.name')
                                    ->label('Performed By')
                                    ->disabled(),

                                TextInput::make('created_at')
                                    ->label('Operation Date')
                                    ->disabled(),

                                Textarea::make('remarks')
                                    ->rows(2)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Installation and AMC Dates')
                            ->columns(2)
                            ->schema([
                                TextInput::make('previous_installation_date')
                                    ->label('Previous Installation Date')
                                    ->disabled(),

                                TextInput::make('new_installation_date')
                                    ->label('New Installation Date')
                                    ->disabled(),

                                TextInput::make('previous_amc_expiry_date')
                                    ->label('Previous AMC Expiry')
                                    ->disabled(),

                                TextInput::make('new_amc_expiry_date')
                                    ->label('New AMC Expiry')
                                    ->disabled(),
                            ]),

                        Section::make('Previous Configuration')
                            ->schema([
                                Textarea::make('previous_configuration')
                                    ->label('Previous Configuration (JSON)')
                                    ->rows(12)
                                    ->formatStateUsing(
                                        fn($state): string => is_array($state)
                                            ? json_encode(
                                                $state,
                                                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                            )
                                            : (string) ($state ?? '')
                                    )
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('New Configuration')
                            ->schema([
                                Textarea::make('new_configuration')
                                    ->label('New Configuration (JSON)')
                                    ->rows(12)
                                    ->formatStateUsing(
                                        fn($state): string => is_array($state)
                                            ? json_encode(
                                                $state,
                                                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                            )
                                            : (string) ($state ?? '')
                                    )
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Licence Key History')
                            ->columns(2)
                            ->schema([
                                Textarea::make('previous_license_key')
                                    ->label('Previous Licence Key')
                                    ->rows(4)
                                    ->disabled(),

                                Textarea::make('new_license_key')
                                    ->label('New Licence Key')
                                    ->rows(4)
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }
}

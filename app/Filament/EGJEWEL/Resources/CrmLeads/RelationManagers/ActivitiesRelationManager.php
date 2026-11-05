<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\RelationManagers;

use App\Models\CrmLeadActivity;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activity History';

    protected static ?string $recordTitleAttribute = 'subject';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_type')
                    ->label('Activity')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'phone_call' => 'Phone Call',
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                        'meeting' => 'Meeting',
                        'follow_up' => 'Follow-up',
                        'note' => 'Internal Note',
                        default => str($state)->replace('_', ' ')->title()->toString(),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'phone_call' => 'info',
                        'whatsapp' => 'success',
                        'email' => 'primary',
                        'meeting' => 'warning',
                        'follow_up' => 'danger',
                        'note' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->placeholder('System'),

                TextColumn::make('activity_at')
                    ->label('Activity Date')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                TextColumn::make('next_follow_up_at')
                    ->label('Next Follow-up')
                    ->dateTime('d M Y h:i A')
                    ->placeholder('Not scheduled')
                    ->sortable(),

                TextColumn::make('outcome')
                    ->label('Outcome')
                    ->limit(35)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Details')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('activity_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Log Activity')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Record Lead Activity')
                    ->schema([
                        Section::make('Activity Information')
                            ->columns(2)
                            ->schema([
                                Select::make('activity_type')
                                    ->label('Activity Type')
                                    ->options([
                                        'phone_call' => 'Phone Call',
                                        'whatsapp' => 'WhatsApp',
                                        'email' => 'Email',
                                        'meeting' => 'Meeting',
                                        'follow_up' => 'Follow-up',
                                        'note' => 'Internal Note',
                                    ])
                                    ->required()
                                    ->default('phone_call'),

                                TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),

                                DateTimePicker::make('activity_at')
                                    ->label('Activity Date & Time')
                                    ->default(now())
                                    ->required(),

                                DateTimePicker::make('next_follow_up_at')
                                    ->label('Schedule Next Follow-up')
                                    ->seconds(false)
                                    ->native(false)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Activity Details')
                            ->columns(1)
                            ->schema([
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(4),

                                Textarea::make('outcome')
                                    ->label('Outcome / Remarks')
                                    ->rows(3),
                            ]),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function (CrmLeadActivity $record): void {
                        if ($record->next_follow_up_at) {
                            $record->lead?->update([
                                'next_follow_up_at' => $record->next_follow_up_at,
                            ]);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View Details')
                    ->modalHeading('Lead Activity Details')
                    ->schema([
                        Section::make('Activity Information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('activity_type')
                                    ->label('Activity Type')
                                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                                        'phone_call' => 'Phone Call',
                                        'whatsapp' => 'WhatsApp',
                                        'email' => 'Email',
                                        'meeting' => 'Meeting',
                                        'follow_up' => 'Follow-up',
                                        'note' => 'Internal Note',
                                        default => str($state ?? '')->replace('_', ' ')->title()->toString(),
                                    })
                                    ->disabled(),

                                TextInput::make('subject')
                                    ->disabled(),

                                TextInput::make('user.name')
                                    ->label('Recorded By')
                                    ->disabled(),

                                TextInput::make('activity_at')
                                    ->label('Activity Date')
                                    ->disabled(),

                                TextInput::make('next_follow_up_at')
                                    ->label('Next Follow-up')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Activity Details')
                            ->schema([
                                Textarea::make('description')
                                    ->rows(5)
                                    ->disabled(),

                                Textarea::make('outcome')
                                    ->rows(3)
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }
}

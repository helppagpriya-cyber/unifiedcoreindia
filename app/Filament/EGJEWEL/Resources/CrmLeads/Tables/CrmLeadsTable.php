<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\CrmLead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\CRM\ConvertCrmLeadToErpClient;
use Filament\Notifications\Notification;
use RuntimeException;
use Throwable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\EGJEWEL\Resources\ErpClients\ErpClientResource;

class CrmLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Firm Name / Contact Person')
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
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('existing_software')
                    ->label('Existing Software')
                    ->searchable()
                    ->placeholder('Not specified'),

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
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->searchable(),

                TextColumn::make('next_follow_up_at')
                    ->label('Next Follow-up')
                    ->dateTime('d M Y, h:i A')
                    ->placeholder('Not scheduled')
                    ->sortable(),

                // Secondary fields stay available from the column selector without crowding the work queue.
                TextColumn::make('lead_number')
                    ->label('Lead No.')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Not provided'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Not provided'),

                TextColumn::make('business_type')
                    ->label('Business Type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product_interested')
                    ->label('Product Interested')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('state')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('convertedErpClient.company_name')
                    ->label('ERP Client')
                    ->url(fn($record): ?string => $record->converted_erp_client_id
                        ? ErpClientResource::getUrl('edit', ['record' => $record->converted_erp_client_id])
                        : null)
                    ->openUrlInNewTab()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lead_date')
                    ->label('Lead Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                SelectFilter::make('status_id')
                    ->label('Lead Status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload()
                    ->optionsLimit(20),

                SelectFilter::make('source_id')
                    ->label('Lead Source')
                    ->relationship('source', 'name')
                    ->searchable()
                    ->preload()
                    ->optionsLimit(20),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->preload()
                    ->searchable()
                    ->optionsLimit(50),

                SelectFilter::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->preload(),

                Filter::make('existing_software')
                    ->label('Existing Software')
                    ->schema([
                        Select::make('value')
                            ->label('Existing software')
                            ->searchable()
                            ->optionsLimit(50)

                            ->getSearchResultsUsing(fn(string $search): array => CrmLead::query()
                                ->whereNotNull('existing_software')
                                ->where('existing_software', '!=', '')
                                ->when(trim($search) !== '', fn(Builder $query) => $query->where('existing_software', 'like', '%' . trim($search) . '%'))
                                ->distinct()
                                ->orderBy('existing_software')
                                ->limit(50)
                                ->pluck('existing_software', 'existing_software')
                                ->all())
                            ->getOptionLabelUsing(fn($value): ?string => filled($value) ? (string) $value : null),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->where('existing_software', $data['value'])
                        : $query),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                Action::make('convertToClient')
                    ->label('Convert to client')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Convert this lead to an ERP client?')
                    ->modalDescription('A new ERP client will be created from the lead details, the lead will be marked as won, and its next follow-up will be cleared.')
                    ->visible(fn($record): bool => blank($record->converted_at))
                    ->action(function ($record): void {
                        try {
                            $client = app(ConvertCrmLeadToErpClient::class)->convert($record);

                            Notification::make()
                                ->title('Lead converted')
                                ->body("ERP client {$client->client_code} is ready.")
                                ->success()
                                ->send();
                        } catch (RuntimeException $exception) {
                            Notification::make()
                                ->title('Could not convert lead')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        } catch (Throwable $exception) {
                            report($exception);

                            Notification::make()
                                ->title('Could not convert lead')
                                ->body('An unexpected error occurred. Please check the application log.')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('assignToMe')
                    ->label('Assign to me')
                    ->icon('heroicon-o-user-plus')
                    ->color('gray')
                    ->visible(fn($record): bool => blank($record->assigned_to))
                    ->action(function ($record): void {
                        DB::transaction(function () use ($record): void {
                            $record->update(['assigned_to' => Auth::id()]);
                            $record->assignments()->create([
                                'assigned_to' => Auth::id(),
                                'assigned_by' => Auth::id(),
                                'assigned_at' => now(),
                                'remarks' => 'Self-assigned from the lead list.',
                            ]);
                        });
                    })
                    ->successNotificationTitle('Lead assigned to you'),
                Action::make('logCall')
                    ->label('Log call')
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->modalHeading('Log a customer call')
                    ->schema([
                        Textarea::make('outcome')
                            ->label('Call outcome')
                            ->required()
                            ->rows(3),
                        DateTimePicker::make('next_follow_up_at')
                            ->label('Schedule next follow-up')
                            ->seconds(false)
                            ->native(false)
                            ->minDate(now()),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->activities()->create([
                            'user_id' => Auth::id(),
                            'activity_type' => 'phone_call',
                            'subject' => 'Customer call',
                            'outcome' => $data['outcome'],
                            'activity_at' => now(),
                            'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                        ]);

                        if (filled($data['next_follow_up_at'] ?? null)) {
                            $record->update(['next_follow_up_at' => $data['next_follow_up_at']]);
                        }
                    })
                    ->successNotificationTitle('Call logged'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assignSelectedLeads')
                        ->label('Assign to executive')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->modalHeading('Assign selected leads')
                        ->modalDescription('Choose an executive. Assignment history will be recorded for each lead whose owner changes.')
                        ->schema([
                            Select::make('assigned_to')
                                ->label('Sales executive')
                                ->options(fn(): array => User::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $newAssignee = (int) $data['assigned_to'];
                            $updated = 0;

                            DB::transaction(function () use ($records, $newAssignee, &$updated): void {
                                foreach ($records as $record) {
                                    if ((int) $record->assigned_to === $newAssignee) {
                                        continue;
                                    }

                                    $record->update(['assigned_to' => $newAssignee]);
                                    $record->assignments()->create([
                                        'assigned_to' => $newAssignee,
                                        'assigned_by' => Auth::id(),
                                        'assigned_at' => now(),
                                        'remarks' => 'Bulk reassigned from the lead list.',
                                    ]);
                                    $updated++;
                                }
                            });

                            Notification::make()
                                ->title('Lead assignment updated')
                                ->body("{$updated} lead(s) assigned.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}

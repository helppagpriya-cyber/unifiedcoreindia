<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Exports\CrmLeadImportTemplateExport;
use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use App\Models\CrmLead;
use App\Services\CRM\CrmLeadImportPreviewService;
use App\Services\CRM\CrmLeadImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Livewire;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

set_time_limit(120);

class ListCrmLeads extends ListRecords
{
    protected static string $resource = CrmLeadResource::class;

    public function getTabs(): array
    {
        $userId = auth()->id();
        $openLeads = fn (Builder $query): Builder => $query
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn (Builder $statusQuery) => $statusQuery->where('is_final', true));
        $myOpenLeads = fn () => $openLeads(CrmLead::query()->where('assigned_to', $userId));
        $unassignedLeads = fn () => $openLeads(CrmLead::query()->whereNull('assigned_to'));
        $dueFollowUps = fn () => CrmLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn (Builder $statusQuery) => $statusQuery->where('is_final', true));
        $upcomingFollowUps = fn () => CrmLead::query()
            ->where('assigned_to', $userId)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '>', now())
            ->whereNull('converted_at')
            ->whereDoesntHave('status', fn (Builder $statusQuery) => $statusQuery->where('is_final', true));
        $highPriorityLeads = fn () => $openLeads(CrmLead::query()
            ->where('assigned_to', $userId)
            ->whereIn('priority', ['high', 'urgent']));
        $wonThisMonth = fn () => CrmLead::query()
            ->where('assigned_to', $userId)
            ->whereBetween('converted_at', [now()->startOfMonth(), now()->endOfMonth()]);

        return [
            'all' => Tab::make('All Leads')
                ->key('all')
                ->badge(fn (): int => CrmLead::query()->count()),
            'my-open' => Tab::make('My Open Leads')
                ->key('my-open')
                ->badge(fn (): int => $myOpenLeads()->count())
                ->query(fn (Builder $query): Builder => $openLeads($query->where('assigned_to', $userId))),
            'unassigned' => Tab::make('Unassigned')
                ->key('unassigned')
                ->badge(fn (): int => $unassignedLeads()->count())
                ->query(fn (Builder $query): Builder => $openLeads($query->whereNull('assigned_to'))),
            'due' => Tab::make('Follow-ups Due')
                ->key('due')
                ->badge(fn (): int => $dueFollowUps()->count())
                ->query(fn (Builder $query): Builder => $query
                    ->where('assigned_to', $userId)
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<=', now())
                    ->whereNull('converted_at')
                    ->whereDoesntHave('status', fn (Builder $statusQuery) => $statusQuery->where('is_final', true))),
            'upcoming' => Tab::make('Upcoming Follow-ups')
                ->key('upcoming')
                ->badge(fn (): int => $upcomingFollowUps()->count())
                ->query(fn (Builder $query): Builder => $query
                    ->where('assigned_to', $userId)
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '>', now())
                    ->whereNull('converted_at')
                    ->whereDoesntHave('status', fn (Builder $statusQuery) => $statusQuery->where('is_final', true))),
            'high-priority' => Tab::make('High Priority')
                ->key('high-priority')
                ->badge(fn (): int => $highPriorityLeads()->count())
                ->query(fn (Builder $query): Builder => $openLeads($query
                    ->where('assigned_to', $userId)
                    ->whereIn('priority', ['high', 'urgent']))),
            'won-month' => Tab::make('Won This Month')
                ->key('won-month')
                ->badge(fn (): int => $wonThisMonth()->count())
                ->query(fn (Builder $query): Builder => $query
                    ->where('assigned_to', $userId)
                    ->whereBetween('converted_at', [now()->startOfMonth(), now()->endOfMonth()])),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalHeading('Download CRM Lead Import Template')
                ->modalDescription('Choose your preferred file format.')
                ->schema([
                    Radio::make('format')
                        ->label('File format')
                        ->options(['xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV (.csv)'])
                        ->default('xlsx')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $export = new CrmLeadImportTemplateExport();

                    return ($data['format'] ?? 'xlsx') === 'csv'
                        ? Excel::download($export, 'crm-lead-import-template.csv', ExcelWriter::CSV)
                        : Excel::download($export, 'crm-lead-import-template.xlsx');
                }),

            Action::make('importLeads')
                ->label('Import Leads')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Import CRM Leads')
                ->modalDescription('Upload an Excel or CSV file up to 50 MB. Validation checks populated rows, then opens the searchable review modal.')
                ->modalSubmitActionLabel('Validate File')
                ->schema([
                    FileUpload::make('import_file')
                        ->label('Excel / CSV file')
                        ->disk('local')
                        ->directory('crm-lead-imports')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                            'application/csv',
                        ])
                        ->maxSize(51200)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $disk = Storage::disk('local');
                    $path = $data['import_file'] ?? null;

                    if (! is_string($path) || ! $disk->exists($path)) {
                        Notification::make()->title('Import file not found')->body('Please upload the file again.')->danger()->send();

                        return;
                    }

                    try {
                        $disk->delete(array_filter([
                            session('crm_lead_import_file'),
                            session('crm_lead_import_preview_file'),
                        ]));
                        session()->forget([
                            'crm_lead_import_file',
                            'crm_lead_import_preview_file',
                            'crm_lead_import_summary',
                        ]);

                        $preview = app(CrmLeadImportPreviewService::class)->preview($path, 'local');
                        foreach ($preview['rows'] as &$row) {
                            $row['include_in_import'] = ($row['status'] ?? null) === 'ready';
                        }
                        unset($row);
                        $previewPath = $path . '.preview.json';

                        $disk->put($previewPath, json_encode($preview, JSON_THROW_ON_ERROR));
                        session()->put([
                            'crm_lead_import_file' => $path,
                            'crm_lead_import_preview_file' => $previewPath,
                            'crm_lead_import_summary' => $this->summarize($preview['rows'] ?? []),
                        ]);

                        $this->replaceMountedAction('reviewImport');
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Import validation failed')
                            ->body('Check the file headings and data, then try again. The application log has the technical details.')
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),

            Action::make('reviewImport')
                ->extraAttributes(['style' => 'display: none;'])
                ->modalHeading('Review CRM Lead Import')
                ->modalDescription('Filter, inspect, correct, and select rows below. Open a row to see the complete data and the exact duplicate value.')
                ->modalWidth('7xl')
                ->modalCancelActionLabel('Keep review open later')
                ->schema([
                    Livewire::make(\App\Livewire\Filament\CrmLeadImportReviewTable::class)
                        ->key('crm-lead-import-review-table'),
                ])
                ->requiresConfirmation()
                ->modalSubmitActionLabel('Continue to import confirmation')
                ->action(function (): void {
                    $filePath = session('crm_lead_import_file');
                    $previewPath = session('crm_lead_import_preview_file');
                    $disk = Storage::disk('local');

                    if (! is_string($filePath) || ! is_string($previewPath) || ! $disk->exists($filePath) || ! $disk->exists($previewPath)) {
                        Notification::make()->title('Import review expired')->body('Upload and validate the file again.')->danger()->send();

                        return;
                    }

                    try {
                        $preview = json_decode($disk->get($previewPath), true, flags: JSON_THROW_ON_ERROR);
                        $selected = collect($preview['rows'] ?? [])->filter(fn ($row) => (bool) ($row['include_in_import'] ?? false))->count();

                        if ($selected < 1) {
                            Notification::make()->title('No rows selected')->body('Include at least one Ready or Possible duplicate row before importing.')->warning()->send();

                            return;
                        }

                        $batch = app(CrmLeadImportService::class)->import($preview, $filePath);
                        $disk->delete([$filePath, $previewPath]);
                        session()->forget([
                            'crm_lead_import_file',
                            'crm_lead_import_preview_file',
                            'crm_lead_import_summary',
                        ]);

                        $notification = Notification::make()
                            ->title('CRM import completed')
                            ->body("Batch #{$batch->id} · Imported {$batch->successful_rows} · Skipped {$batch->skipped_rows} · Failed {$batch->failed_rows}")
                            ->persistent();

                        $batch->failed_rows > 0 ? $notification->warning() : $notification->success();
                        $notification->send();
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Import failed')
                            ->body('The review is still available. Check the application log and try again.')
                            ->persistent()
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('resumeImportReview')
                ->label('Resume import review')
                ->icon('heroicon-o-clipboard-document-check')
                ->visible(fn (): bool => filled(session('crm_lead_import_preview_file')))
                ->action(fn () => $this->replaceMountedAction('reviewImport')),

            Action::make('discardImportReview')
                ->label('Discard review')
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->visible(fn (): bool => filled(session('crm_lead_import_preview_file')))
                ->requiresConfirmation()
                ->action(function (): void {
                    $disk = Storage::disk('local');
                    $disk->delete(array_filter([
                        session('crm_lead_import_file'),
                        session('crm_lead_import_preview_file'),
                    ]));
                    session()->forget([
                        'crm_lead_import_file',
                        'crm_lead_import_preview_file',
                        'crm_lead_import_summary',
                    ]);
                    Notification::make()->title('Import review discarded')->success()->send();
                }),

            CreateAction::make()
                ->label('Create Lead')
                ->icon('heroicon-o-plus'),
        ];
    }

    private function summarize(array $rows): array
    {
        $collection = collect($rows);

        return [
            'total' => $collection->count(),
            'ready' => $collection->where('status', 'ready')->count(),
            'invalid' => $collection->where('status', 'invalid')->count(),
            'duplicates' => $collection->where('status', 'duplicate')->count(),
            'possible_duplicates' => $collection->where('status', 'possible_duplicate')->count(),
            'included' => $collection->filter(fn ($row) => (bool) ($row['include_in_import'] ?? false))->count(),
        ];
    }
}

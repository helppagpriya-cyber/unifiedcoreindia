<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Exports\CrmLeadImportTemplateExport;
use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use App\Services\CRM\CrmLeadImportPreviewService;
use App\Services\CRM\CrmLeadImportService;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Throwable;

class ListCrmLeads extends ListRecords
{
    protected static string $resource = CrmLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | DOWNLOAD TEMPLATE
            |--------------------------------------------------------------------------
            */

            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalHeading('Download CRM Lead Import Template')
                ->modalDescription('Choose your preferred file format.')
                ->modalSubmitActionLabel('Download')
                ->schema([
                    Radio::make('format')
                        ->label('Select File Format')
                        ->options([
                            'xlsx' => 'Excel File (.xlsx)',
                            'csv' => 'CSV File (.csv)',
                        ])
                        ->default('xlsx')
                        ->required(),
                ])
                ->action(function (array $data) {

                    $export = new CrmLeadImportTemplateExport();

                    if (($data['format'] ?? 'xlsx') === 'csv') {
                        return Excel::download(
                            $export,
                            'crm-lead-import-template.csv',
                            ExcelWriter::CSV
                        );
                    }

                    return Excel::download(
                        $export,
                        'crm-lead-import-template.xlsx'
                    );
                }),

            /*
            |--------------------------------------------------------------------------
            | STEP 1: UPLOAD FILE AND GENERATE PREVIEW
            |--------------------------------------------------------------------------
            */

            Action::make('importLeads')
                ->label('Import Leads')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Import CRM Leads')
                ->modalDescription(
                    'Upload your Excel or CSV file to validate and review the leads before importing.'
                )
                ->modalSubmitActionLabel('Generate Summary')
                ->schema([
                    FileUpload::make('import_file')
                        ->label('Select Excel / CSV File')
                        ->disk('local')
                        ->directory('crm-lead-imports')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                            'text/plain',
                            'application/csv',
                        ])
                        ->maxSize(10240)
                        ->required(),
                ])
                ->action(function (array $data) {

                    $path = $data['import_file'] ?? null;

                    if (! $path || ! Storage::disk('local')->exists($path)) {
                        Notification::make()
                            ->title('Import file not found')
                            ->body('Please upload the file again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {

                        $preview = app(CrmLeadImportPreviewService::class)
                            ->preview($path, 'local');

                        /*
                         * Save original row count BEFORE storing
                         * the preview in the session.
                         */
                        $preview['original_total'] = count(
                            $preview['rows'] ?? []
                        );

                        $preview['removed_rows'] = 0;

                        /*
                         * Select only Ready rows by default.
                         */
                        foreach ($preview['rows'] as &$row) {
                            $row['include_in_import'] =
                                ($row['status'] ?? '') === 'ready';
                        }

                        unset($row);

                        session()->put('crm_lead_import_preview', $preview);
                        session()->put('crm_lead_import_file', $path);

                        /*
                         * Automatically open the review modal.
                         */
                        $this->replaceMountedAction('reviewImport');
                    } catch (Throwable $exception) {

                        report($exception);

                        Notification::make()
                            ->title('Import preview failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),

            /*
            |--------------------------------------------------------------------------
            | STEP 2: REVIEW SUMMARY AND ROWS
            |--------------------------------------------------------------------------
            |
            | This action has no visible header button.
            | It opens automatically after uploading the file.
            |
            */

            Action::make('reviewImport')

                ->extraAttributes([
                    'style' => 'display: none;',
                ])
                ->modalHeading('Review CRM Lead Import')
                ->modalDescription(
                    'Review the validation results, remove unwanted rows, and select any possible duplicates you want to import.'
                )
                ->modalWidth('7xl')
                ->modalSubmitActionLabel('Continue to Confirm Import')
                ->modalCancelActionLabel('Cancel')
                ->schema([

                    /*
                     * Dynamic summary.
                     */
                    \Filament\Forms\Components\Placeholder::make('import_summary')
                        ->label('Import Summary')
                        ->content(function (Get $get) {

                            $rows = collect($get('rows') ?? []);

                            $originalTotal = (int) session(
                                'crm_lead_import_preview.original_total',
                                0
                            );

                            $removed = max(
                                0,
                                $originalTotal - $rows->count()
                            );

                            $ready = $rows->where('status', 'ready')->count();

                            $invalid = $rows->where('status', 'invalid')->count();

                            $duplicates = $rows->where('status', 'duplicate')->count();

                            $possible = $rows
                                ->where('status', 'possible_duplicate')
                                ->count();

                            $selected = $rows
                                ->filter(fn($row) => (bool) ($row['include_in_import'] ?? false))
                                ->count();

                            return
                                "Original: {$originalTotal}"
                                . " | Remaining: {$rows->count()}"
                                . " | Ready: {$ready}"
                                . " | Invalid: {$invalid}"
                                . " | Duplicates: {$duplicates}"
                                . " | Review Required: {$possible}"
                                . " | Selected: {$selected}"
                                . " | Removed: {$removed}";
                        }),

                    /*
                     * Bulk removal action inside the review modal.
                     */
                    Actions::make([
                        Action::make('removeInvalidAndDuplicates')
                            ->label('Remove All Invalid & Duplicates')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Remove Invalid and Duplicate Rows?')
                            ->modalDescription(
                                'This removes Invalid and exact Duplicate rows from the current preview only. Existing CRM records will not be affected. Review Required rows will remain.'
                            )
                            ->modalSubmitActionLabel('Yes, Remove Rows')
                            ->action(function (Get $get, Set $set) {

                                $visibleRows = collect($get('rows') ?? []);

                                /*
                                 * Always check original statuses from
                                 * the server-side session.
                                 */
                                $originalRows = collect(
                                    session('crm_lead_import_preview.rows', [])
                                )->keyBy(
                                    fn($row) => (string) ($row['row_number'] ?? '')
                                );

                                $remainingRows = $visibleRows
                                    ->reject(function ($row) use ($originalRows) {

                                        $original = $originalRows->get(
                                            (string) ($row['row_number'] ?? '')
                                        );

                                        return in_array(
                                            $original['status'] ?? '',
                                            ['invalid', 'duplicate'],
                                            true
                                        );
                                    });

                                $removedCount =
                                    $visibleRows->count() - $remainingRows->count();

                                $set('rows', $remainingRows->all());

                                Notification::make()
                                    ->title('Rows removed from preview')
                                    ->body(
                                        "{$removedCount} rows removed. Existing CRM records were not changed."
                                    )
                                    ->success()
                                    ->send();
                            }),
                    ]),

                    /*
                     * Row-by-row preview.
                     */
                    Repeater::make('rows')
                        ->label('Import Rows')
                        ->table([
                            TableColumn::make('Excel Row'),
                            TableColumn::make('Company'),
                            TableColumn::make('Mobile'),
                            TableColumn::make('Result'),
                            TableColumn::make('Include'),
                            TableColumn::make('Details'),
                        ])
                        ->schema([

                            TextInput::make('row_number')
                                ->label('Excel Row')
                                ->disabled()
                                ->dehydrated(true),

                            TextInput::make('data.company_name')
                                ->label('Company')
                                ->disabled(),

                            TextInput::make('data.mobile')
                                ->label('Mobile')
                                ->disabled(),

                            TextInput::make('status')
                                ->label('Result')
                                ->disabled()
                                ->dehydrated(true),

                            Toggle::make('include_in_import')
                                ->label('Include'),

                            Textarea::make('details')
                                ->label('Details')
                                ->disabled()
                                ->rows(2)
                                ->dehydrated(false),
                        ])
                        ->default(function () {

                            $rows = session(
                                'crm_lead_import_preview.rows',
                                []
                            );

                            return collect($rows)
                                ->map(function ($row) {

                                    $details = array_filter([
                                        $row['duplicate_message'] ?? null,
                                        implode('; ', $row['errors'] ?? []),
                                    ]);

                                    $row['details'] = implode(' | ', $details);

                                    $row['include_in_import'] =
                                        $row['include_in_import']
                                        ?? (($row['status'] ?? '') === 'ready');

                                    return $row;
                                })
                                ->values()
                                ->all();
                        })
                        ->addable(false)
                        ->deletable(true)
                        ->reorderable(false)
                        ->collapsible(false),
                ])
                ->action(function (array $data) {

                    $preview = session('crm_lead_import_preview');

                    if (! is_array($preview)) {
                        Notification::make()
                            ->title('Import preview expired')
                            ->body('Please upload your file again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    /*
                     * Use the server-side preview as the source
                     * of truth. Accept only row removals and
                     * include_in_import choices from the form.
                     */
                    $previousRows = collect($preview['rows'] ?? []);

                    $originalRows = $previousRows->keyBy(
                        fn($row) => (string) ($row['row_number'] ?? '')
                    );

                    $remainingRows = [];

                    foreach (($data['rows'] ?? []) as $submittedRow) {

                        $rowNumber = (string) (
                            $submittedRow['row_number'] ?? ''
                        );

                        $originalRow = $originalRows->get($rowNumber);

                        if (! $originalRow) {
                            continue;
                        }

                        $originalRow['include_in_import'] =
                            (bool) ($submittedRow['include_in_import'] ?? false);

                        $remainingRows[] = $originalRow;
                    }

                    $removedNow = max(
                        0,
                        $previousRows->count() - count($remainingRows)
                    );

                    $preview['removed_rows'] =
                        (int) ($preview['removed_rows'] ?? 0) + $removedNow;

                    $preview['rows'] = $remainingRows;

                    $this->updateImportSummary($preview);

                    session()->put('crm_lead_import_preview', $preview);

                    /*
                     * Automatically open final confirmation.
                     */
                    $this->replaceMountedAction('confirmImport');
                }),

            /*
            |--------------------------------------------------------------------------
            | STEP 3: CONFIRM AND SAVE TO DATABASE
            |--------------------------------------------------------------------------
            |
            | This action also has no visible header button.
            |
            */

            Action::make('confirmImport')
                ->extraAttributes([
                    'style' => 'display: none;',
                ])

                ->requiresConfirmation()
                ->modalHeading('Confirm CRM Lead Import')
                ->modalDescription(
                    'Are you sure you want to import the selected leads? Manually approved duplicates will create new CRM lead records. Existing CRM records will not be modified.'
                )
                ->modalSubmitActionLabel('Yes, Import Selected Leads')
                ->action(function () {

                    $preview = session('crm_lead_import_preview');
                    $filePath = session('crm_lead_import_file');

                    if (! is_array($preview) || ! is_string($filePath)) {

                        Notification::make()
                            ->title('Import preview not found')
                            ->body('Please upload and preview your file again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (! Storage::disk('local')->exists($filePath)) {

                        Notification::make()
                            ->title('Import file not found')
                            ->body('Please upload your file again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {

                        $batch = app(CrmLeadImportService::class)
                            ->import($preview, $filePath);

                        /*
                         * Delete temporary data only after
                         * the import service has completed.
                         */
                        Storage::disk('local')->delete($filePath);

                        session()->forget([
                            'crm_lead_import_preview',
                            'crm_lead_import_file',
                        ]);

                        $message =
                            'Batch #' . $batch->id
                            . ' | Total: ' . $batch->total_rows
                            . ' | Imported: ' . $batch->successful_rows
                            . ' | Skipped: ' . $batch->skipped_rows
                            . ' | Failed: ' . $batch->failed_rows;

                        $notification = Notification::make()
                            ->title('CRM Lead Import Completed')
                            ->body($message)
                            ->persistent();

                        if ($batch->failed_rows > 0) {
                            $notification->warning();
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    } catch (Throwable $exception) {

                        report($exception);

                        Notification::make()
                            ->title('Import failed')
                            ->body(
                                'The import could not be completed. Please check the application log.'
                            )
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),

            /*
            |--------------------------------------------------------------------------
            | EXISTING CREATE LEAD ACTION
            |--------------------------------------------------------------------------
            */

            CreateAction::make()
                ->label('Create Lead')
                ->icon('heroicon-o-plus'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE IMPORT SUMMARY
    |--------------------------------------------------------------------------
    */

    protected function updateImportSummary(array &$preview): void
    {
        $rows = collect($preview['rows'] ?? []);

        $preview['summary'] = [
            'total' => (int) ($preview['original_total'] ?? $rows->count()),

            'remaining' => $rows->count(),

            'ready' => $rows->where('status', 'ready')->count(),

            'invalid' => $rows->where('status', 'invalid')->count(),

            'duplicates' => $rows->where('status', 'duplicate')->count(),

            'possible_duplicates' => $rows
                ->where('status', 'possible_duplicate')
                ->count(),

            'removed' => (int) ($preview['removed_rows'] ?? 0),
        ];
    }
}

<?php

namespace App\Livewire\Filament;

use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;
use App\Models\User;
use App\Services\CRM\CrmLeadImportPreviewService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CrmLeadImportReviewTable extends TableComponent
{
    protected string $view = 'filament.egjewel.components.crm-lead-import-review-table';

    public string $statusFilter = 'all';

    public string $search = '';

    public function render(): View
    {
        return view($this->view, $this->getViewData());
    }

    protected ?array $cachedPreview = null;

    protected array $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->paginatedRows())
            ->columns([
                TextColumn::make('row_number')
                    ->label('Excel row'),
                TextColumn::make('data.company_name')
                    ->label('Company')
                    ->description(fn (array $record): string => 'Row ' . ($record['row_number'] ?? '—'))
                    ->wrap(),
                TextColumn::make('data.contact_person')
                    ->label('Contact')
                    ->placeholder('Not provided'),
                TextColumn::make('data.mobile')
                    ->label('Phone')
                    ->placeholder('Not provided')
                    ->copyable(),
                TextColumn::make('status')
                    ->label('Review result')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ready' => 'Ready',
                        'invalid' => 'Needs correction',
                        'duplicate' => 'Exact duplicate',
                        'possible_duplicate' => 'Possible duplicate',
                        default => 'Unknown',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ready' => 'success',
                        'invalid' => 'danger',
                        'duplicate' => 'warning',
                        'possible_duplicate' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('duplicate_message')
                    ->label('Why flagged')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('errors')
                    ->label('What to fix')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? implode('; ', $state) : (string) ($state ?? ''))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('include_in_import')
                    ->label('Import')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Included' : 'Skipped')
                    ->color(fn ($state): string => $state ? 'success' : 'gray'),
            ])
            ->recordActions([
                Action::make('reviewRow')
                    ->label('Review / fix')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (array $record): string => 'Review Excel row ' . ($record['row_number'] ?? ''))
                    ->modalWidth('5xl')
                    ->fillForm(fn (array $record): array => $record['data'] ?? [])
                    ->schema([
                        Placeholder::make('row_review_notes')
                            ->label('Validation and duplicate details')
                            ->content(function (array $record): string {
                                return implode(' · ', array_filter([
                                    implode('; ', $record['errors'] ?? []),
                                    $record['duplicate_message'] ?? null,
                                ])) ?: 'No validation issues. Review the values and save if you make a change.';
                            }),
                        TextInput::make('company_name')->label('Company name')->required()->maxLength(255),
                        TextInput::make('contact_person')->label('Contact person')->maxLength(255),
                        TextInput::make('mobile')->label('Mobile')->tel()->maxLength(20),
                        TextInput::make('alternate_mobile')->label('Alternate mobile')->tel()->maxLength(20),
                        TextInput::make('whatsapp_number')->label('WhatsApp number')->tel()->maxLength(20),
                        TextInput::make('email')->label('Email')->email()->maxLength(255),
                        TextInput::make('business_type')->label('Business type')->maxLength(255),
                        TextInput::make('gst_number')->label('GST number')->maxLength(20),
                        TextInput::make('pan_number')->label('PAN number')->maxLength(20),
                        TextInput::make('city')->label('City')->maxLength(255),
                        TextInput::make('state')->label('State')->maxLength(255),
                        TextInput::make('pincode')->label('PIN code')->maxLength(15),
                        TextInput::make('country')->label('Country')->maxLength(255),
                        TextInput::make('product_interested')->label('Product interested in')->maxLength(255),
                        TextInput::make('existing_software')->label('Existing software')->maxLength(255),
                        TextInput::make('estimated_budget')->label('Estimated budget')->numeric()->minValue(0)->prefix('₹'),
                        Select::make('source')
                            ->label('Lead source')
                            ->options(fn (): array => CrmLeadSource::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'name')->all())
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->label('Lead status')
                            ->options(fn (): array => CrmLeadStatus::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'name')->all())
                            ->searchable()
                            ->required(),
                        Select::make('priority')
                            ->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])
                            ->required(),
                        Select::make('assigned_to_email')
                            ->label('Assigned executive')
                            ->options(fn (): array => User::query()->where('is_active', true)->whereNotNull('email')->orderBy('name')->get(['name', 'email'])->mapWithKeys(fn (User $user): array => [$user->email => "{$user->name} · {$user->email}"])->all())
                            ->searchable()
                            ->placeholder('Unassigned'),
                        DatePicker::make('lead_date')->label('Lead date'),
                        DateTimePicker::make('next_follow_up_at')->label('Next follow-up')->seconds(false),
                        Textarea::make('address')->label('Address')->rows(2)->columnSpanFull(),
                        Textarea::make('requirements')->label('Requirements')->rows(3)->columnSpanFull(),
                        Textarea::make('notes')->label('Internal notes')->rows(2)->columnSpanFull(),
                    ])
                    ->action(function (array $data, array $record): void {
                        $preview = $this->loadPreview();
                        $index = $this->findRowIndex($preview['rows'] ?? [], (int) ($record['row_number'] ?? 0));

                        if ($index === null) {
                            Notification::make()->title('Preview row not found')->danger()->send();

                            return;
                        }

                        $row = $preview['rows'][$index];
                        $row['data'] = array_merge($row['data'] ?? [], $data);
                        $preview['rows'][$index] = app(CrmLeadImportPreviewService::class)->revalidateRow($row, $preview['rows']);
                        $this->savePreview($preview);
                        $this->resetPage();

                        Notification::make()
                            ->title('Row checked again')
                            ->body($preview['rows'][$index]['status'] === 'ready'
                                ? 'The row is valid and selected for import.'
                                : 'Review the updated status and messages in the row list.')
                            ->success()
                            ->send();
                    }),
                Action::make('toggleImportSelection')
                    ->label(fn (array $record): string => ($record['include_in_import'] ?? false) ? 'Remove from import' : 'Include row')
                    ->icon(fn (array $record): string => ($record['include_in_import'] ?? false) ? 'heroicon-o-minus-circle' : 'heroicon-o-check-circle')
                    ->color(fn (array $record): string => ($record['include_in_import'] ?? false) ? 'gray' : 'success')
                    ->visible(fn (array $record): bool => in_array($record['status'] ?? null, ['ready', 'possible_duplicate'], true))
                    ->action(function (array $record): void {
                        $preview = $this->loadPreview();
                        $index = $this->findRowIndex($preview['rows'] ?? [], (int) ($record['row_number'] ?? 0));

                        if ($index === null || ! in_array($preview['rows'][$index]['status'] ?? null, ['ready', 'possible_duplicate'], true)) {
                            return;
                        }

                        $preview['rows'][$index]['include_in_import'] = ! (bool) ($preview['rows'][$index]['include_in_import'] ?? false);
                        $this->savePreview($preview);
                    })
                    ->successNotificationTitle('Import selection updated'),
            ])
            ->headerActions([
                Action::make('includeAllPossibleDuplicates')
                    ->label('Include all possible duplicates')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Every possible company-name match will be selected. Exact duplicates and invalid rows remain excluded.')
                    ->action(function (): void {
                        $preview = $this->loadPreview();

                        if (! is_array($preview['rows'] ?? null)) {
                            Notification::make()->title('Preview rows not found')->danger()->send();

                            return;
                        }

                        foreach ($preview['rows'] as &$row) {
                            if (($row['status'] ?? null) === 'possible_duplicate') {
                                $row['include_in_import'] = true;
                            }
                        }
                        unset($row);

                        $this->savePreview($preview);
                        Notification::make()->title('Possible duplicates selected')->success()->send();
                    }),
            ])
            ->defaultSort('row_number')
            ->paginated([25, 50, 100]);
    }

    protected function getViewData(): array
    {
        $preview = $this->loadPreview();

        return [
            'summary' => $preview ? $this->summarize($preview['rows'] ?? []) : [],
        ];
    }

    private function paginatedRows(): LengthAwarePaginator
    {
        $rows = collect($this->loadPreview()['rows'] ?? []);
        $query = mb_strtolower(trim($this->search));

        $filtered = $rows->filter(function (array $row) use ($query): bool {
            if ($this->statusFilter === 'selected' && ! (bool) ($row['include_in_import'] ?? false)) {
                return false;
            }

            if (! in_array($this->statusFilter, ['all', 'selected'], true) && ($row['status'] ?? null) !== $this->statusFilter) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $data = $row['data'] ?? [];
            $searchable = implode(' ', [
                $row['row_number'] ?? '',
                $data['company_name'] ?? '',
                $data['contact_person'] ?? '',
                $data['mobile'] ?? '',
                $data['email'] ?? '',
                $data['gst_number'] ?? '',
                $row['duplicate_message'] ?? '',
                implode(' ', $row['errors'] ?? []),
            ]);

            return str_contains(mb_strtolower($searchable), $query);
        })->sortBy('row_number')->values();

        $perPage = (int) ($this->getTableRecordsPerPage() ?: 25);
        $page = max(1, (int) $this->getTablePage());
        $pageName = $this->getTablePaginationPageName();

        return new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName],
        );
    }

    private function loadPreview(): ?array
    {
        if ($this->cachedPreview !== null) {
            return $this->cachedPreview;
        }

        $path = session('crm_lead_import_preview_file');
        $disk = Storage::disk('local');

        if (! is_string($path) || ! $disk->exists($path)) {
            return null;
        }

        try {
            $this->cachedPreview = json_decode($disk->get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        return $this->cachedPreview;
    }

    private function savePreview(array $preview): void
    {
        $path = session('crm_lead_import_preview_file');
        if (! is_string($path)) {
            return;
        }

        Storage::disk('local')->put($path, json_encode($preview, JSON_THROW_ON_ERROR));
        $this->cachedPreview = $preview;
        session()->put('crm_lead_import_summary', $this->summarize($preview['rows'] ?? []));
        $this->flushCachedTableRecords();
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

    private function findRowIndex(array $rows, int $rowNumber): ?int
    {
        foreach ($rows as $index => $row) {
            if ((int) ($row['row_number'] ?? 0) === $rowNumber) {
                return $index;
            }
        }

        return null;
    }

}

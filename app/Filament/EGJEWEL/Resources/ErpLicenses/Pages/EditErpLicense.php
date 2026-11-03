<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Pages;

use App\Filament\EGJEWEL\Resources\ErpLicenses\ErpLicenseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\ErpLicenseHistoryService;
use App\Models\ErpLicense;
use App\Models\ErpSoftwareModule;
use App\Models\ErpSoftwareVersion;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\LegacyV1LicenseKeyGenerator;


class EditErpLicense extends EditRecord
{

    protected static string $resource = ErpLicenseResource::class;

    protected array $previousConfiguration = [];

    protected ?string $previousLicenseKey = null;
    protected function beforeSave(): void
    {
        $historyService = app(ErpLicenseHistoryService::class);

        $this->previousConfiguration = $historyService->capture($this->record);

        $this->previousLicenseKey = $this->record->license_key;
    }
    protected function afterSave(): void
    {
        $historyService = app(ErpLicenseHistoryService::class);

        $newConfiguration = $historyService->capture($this->record);

        $configurationChanged =
            $this->previousConfiguration !== $newConfiguration;

        $keyChanged =
            $this->previousLicenseKey !== $this->record->license_key;

        // Do not create unnecessary history entries if nothing changed.
        if (! $configurationChanged && ! $keyChanged) {
            return;
        }

        $historyService->recordChange(
            license: $this->record,
            operationType: 'updated',
            previousConfiguration: $this->previousConfiguration,
            previousLicenseKey: $this->previousLicenseKey,
            remarks: 'Licence configuration updated from the admin panel.'
        );
    }

    /**
     * Generate an original VFP Demo Key.
     */
    protected function generateDemoKey(string $qst): void
    {
        $licenseId = $this->record->getKey();

        DB::transaction(function () use ($licenseId, $qst): void {
            $license = ErpLicense::query()
                ->lockForUpdate()
                ->findOrFail($licenseId);

            if ($license->license_type !== 'demo') {
                throw ValidationException::withMessages([
                    'qst' => 'Only demo licences can generate demo keys.',
                ]);
            }

            if (filled($license->license_key)) {
                throw ValidationException::withMessages([
                    'qst' => 'A key has already been generated for this licence.',
                ]);
            }

            // Allocate the next legacy Demo ID.
            $lastDemoId = ErpLicense::withTrashed()
                ->whereNotNull('legacy_demo_id')
                ->orderByDesc('legacy_demo_id')
                ->lockForUpdate()
                ->value('legacy_demo_id');

            $demoId = max(1001, ((int) $lastDemoId) + 1);

            if ($demoId > 9999) {
                throw ValidationException::withMessages([
                    'qst' => 'The available Demo ID range (1001–9999) has been exhausted.',
                ]);
            }

            $historyService = app(ErpLicenseHistoryService::class);

            $previousConfiguration = $historyService->capture($license);
            $previousLicenseKey = $license->license_key;

            try {
                $result = app(LegacyV1LicenseKeyGenerator::class)
                    ->generateDemo(
                        $license,
                        $qst,
                        (string) $demoId,
                    );
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'qst' => $exception->getMessage(),
                ]);
            }

            $license->update([
                'legacy_demo_id' => $demoId,
                'license_key' => $result['license_key'],
                'status' => 'issued',
                'issued_at' => now(),
                'lan_enabled' => $result['lan_enabled'],
            ]);

            $license->refresh();

            $historyService->recordChange(
                license: $license,
                operationType: 'demo_key_generated',
                previousConfiguration: $previousConfiguration,
                previousLicenseKey: $previousLicenseKey,
                remarks: "Legacy VFP demo key generated. Demo ID: {$demoId}.",
            );
        });

        $this->record->refresh();

        $this->showGeneratedKeyPopup();
    }
    /**
     * Generate an original VFP Full Installation Key.
     */
    protected function generateInstallationKey(string $qst): void
    {
        $licenseId = $this->record->getKey();

        DB::transaction(function () use ($licenseId, $qst): void {
            $license = ErpLicense::query()
                ->lockForUpdate()
                ->findOrFail($licenseId);

            if ($license->license_type !== 'full') {
                throw ValidationException::withMessages([
                    'qst' => 'Only full licences can generate installation keys.',
                ]);
            }

            if (filled($license->license_key)) {
                throw ValidationException::withMessages([
                    'qst' => 'A key has already been generated for this licence.',
                ]);
            }

            if (blank($license->legacy_software_id)) {
                throw ValidationException::withMessages([
                    'qst' => 'Please save a valid EG Software ID on the licence before generating its installation key.',
                ]);
            }

            $historyService = app(ErpLicenseHistoryService::class);

            $previousConfiguration = $historyService->capture($license);
            $previousLicenseKey = $license->license_key;

            try {
                $result = app(LegacyV1LicenseKeyGenerator::class)
                    ->generateInstallation(
                        $license,
                        $qst,
                        (string) $license->legacy_software_id,
                    );
            } catch (\InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'qst' => $exception->getMessage(),
                ]);
            }

            $license->update([
                'license_key' => $result['license_key'],
                'status' => 'issued',
                'issued_at' => now(),
                'lan_enabled' => $result['lan_enabled'],
                // AMC expiry is deliberately not changed here.
            ]);

            $license->refresh();

            $historyService->recordChange(
                license: $license,
                operationType: 'installation_key_generated',
                previousConfiguration: $previousConfiguration,
                previousLicenseKey: $previousLicenseKey,
                remarks: 'Legacy VFP full installation key generated.',
            );
        });

        $this->record->refresh();
        $this->showGeneratedKeyPopup();
    }
    protected function showGeneratedKeyPopup(): void
    {
        $this->record->refresh();

        $this->replaceMountedAction('showGeneratedKey');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateDemoKey')
                ->label('Generate Demo Key')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->visible(
                    fn(): bool =>
                    $this->record->license_type === 'demo'
                        && blank($this->record->license_key)
                )
                ->requiresConfirmation()
                ->modalHeading('Generate Legacy VFP Demo Key')
                ->modalDescription('Enter the QST displayed by EGJEWEL for this demo activation.')
                ->form([
                    TextInput::make('qst')
                        ->label('QST Serial Number')
                        ->required()
                        ->minLength(6)
                        ->maxLength(100)
                        ->placeholder('Enter QST from EGJEWEL'),
                ])
                ->modalSubmitActionLabel('Generate Demo Key')
                ->action(fn(array $data) => $this->generateDemoKey($data['qst'])),

            Action::make('generateInstallationKey')
                ->label('Generate Installation Key')
                ->icon('heroicon-o-key')
                ->color('success')
                ->visible(
                    fn(): bool =>
                    $this->record->license_type === 'full'
                        && blank($this->record->license_key)
                )
                ->requiresConfirmation()
                ->modalHeading('Generate Legacy VFP Installation Key')
                ->modalDescription('Enter the QST displayed by EGJEWEL for this installation.')
                ->form([
                    TextInput::make('qst')
                        ->label('QST Serial Number')
                        ->required()
                        ->minLength(6)
                        ->maxLength(100)
                        ->placeholder('Enter QST from EGJEWEL'),
                ])
                ->modalSubmitActionLabel('Generate Installation Key')
                ->action(fn(array $data) => $this->generateInstallationKey($data['qst'])),
            Action::make('showGeneratedKey')
                ->label('Generated Key')
                ->modalHeading(
                    fn() => $this->record->license_type === 'demo'
                        ? 'Generated Demo Licence Key'
                        : 'Generated Installation Licence Key'
                )
                ->modalWidth('2xl')
                ->schema([
                    TextInput::make('reference')
                        ->label(
                            fn() => $this->record->license_type === 'demo'
                                ? 'Demo ID'
                                : 'Software ID'
                        )
                        ->readOnly()
                        ->dehydrated(false),

                    TextInput::make('generated_key')
                        ->label('Generated Licence Key')
                        ->readOnly()
                        ->copyable()


                        ->dehydrated(false),
                ])
                ->fillForm(function (): array {
                    $license = $this->record->fresh();

                    return [
                        'reference' => $license->license_type === 'demo'
                            ? $license->legacy_demo_id
                            : $license->legacy_software_id,

                        'generated_key' => $license->license_key,
                    ];
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->action(fn() => null)
                ->extraAttributes(['class' => 'hidden']),
            Action::make('convertDemoToFull')
                ->label('Convert to Full Licence')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('success')
                ->visible(fn() => $this->record->license_type === 'demo')
                ->modalHeading('Convert Demo to Full Licence')
                ->modalDescription(
                    'This will convert the existing demo licence into a full licence. The previous demo configuration will be saved in licence history.'
                )
                ->modalSubmitActionLabel('Convert Licence')
                ->schema([
                    Select::make('software_version_id')
                        ->label('Full Software Edition')
                        ->options(fn() => ErpSoftwareVersion::query()
                            ->where('software_product_id', $this->record->software_product_id)
                            ->where('is_active', true)
                            ->whereBetween('legacy_edition_number', [1, 30])
                            ->orderBy('legacy_edition_number')
                            ->pluck('version_name', 'id')
                            ->all())
                        ->searchable()
                        ->required(),

                    Select::make('modules')
                        ->label('Enabled Modules')
                        ->multiple()
                        ->options(fn() => ErpSoftwareModule::query()
                            ->where('software_product_id', $this->record->software_product_id)
                            ->where('is_active', true)
                            ->orderBy('legacy_group')
                            ->orderBy('legacy_position')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable(),
                    DatePicker::make('amc_expiry_date')
                        ->label('AMC Expiry Date')
                        ->native(false),

                    Toggle::make('lan_enabled')
                        ->label('LAN Enabled'),

                    Toggle::make('gst_enabled')
                        ->label('GST Enabled'),

                    TextInput::make('sales_points')
                        ->numeric()
                        ->minValue(1),

                    TextInput::make('max_companies')
                        ->numeric()
                        ->minValue(1),

                    TextInput::make('max_simultaneous_users')
                        ->label('Maximum Simultaneous Users')
                        ->numeric()
                        ->minValue(1),

                    TextInput::make('customer_code')
                        ->maxLength(255),
                ])

                ->fillForm(fn() => [
                    'software_version_id' => $this->record->software_version_id,
                    'modules' => $this->record->modules()
                        ->pluck('erp_software_modules.id')
                        ->all(),
                    'amc_expiry_date' => $this->record->amc_expiry_date,
                    'lan_enabled' => $this->record->lan_enabled,
                    'gst_enabled' => $this->record->gst_enabled,
                    'sales_points' => $this->record->sales_points,
                    'max_companies' => $this->record->max_companies,
                    'max_simultaneous_users' => $this->record->max_simultaneous_users,
                    'customer_code' => $this->record->customer_code,
                ])
                ->action(function (array $data): void {

                    // Always retrieve the latest database record.
                    $license = ErpLicense::query()
                        ->findOrFail($this->record->getKey());

                    // Only demo licences can be converted.
                    if ($license->license_type !== 'demo') {
                        throw ValidationException::withMessages([
                            'license' => 'Only demo licences can be converted.',
                        ]);
                    }

                    // Validate the selected edition belongs to this product.
                    $versionExists = ErpSoftwareVersion::query()
                        ->where('id', $data['software_version_id'])
                        ->where('software_product_id', $license->software_product_id)
                        ->where('is_active', true)
                        ->whereBetween('legacy_edition_number', [1, 30])
                        ->exists();

                    if (! $versionExists) {
                        throw ValidationException::withMessages([
                            'software_version_id' => 'Invalid software edition selected.',
                        ]);
                    }

                    // Validate that every selected module belongs to this product.
                    $moduleIds = collect($data['modules'] ?? [])
                        ->map(fn($id) => (int) $id)
                        ->unique()
                        ->values();

                    $validModuleCount = ErpSoftwareModule::query()
                        ->where('software_product_id', $license->software_product_id)
                        ->where('is_active', true)
                        ->whereIn('id', $moduleIds)
                        ->count();

                    if ($validModuleCount !== $moduleIds->count()) {
                        throw ValidationException::withMessages([
                            'modules' => 'One or more selected modules are invalid.',
                        ]);
                    }

                    $historyService = app(ErpLicenseHistoryService::class);

                    // Capture the complete demo configuration before changing it.
                    $previousConfiguration = $historyService->capture($license);

                    $previousLicenseKey = $license->license_key;

                    DB::transaction(function () use (
                        $license,
                        $data,
                        $moduleIds,
                        $historyService,
                        $previousConfiguration,
                        $previousLicenseKey
                    ) {
                        $license->update([
                            'license_type' => 'full',
                            'status' => 'draft',

                            'software_version_id' => $data['software_version_id'],

                            'demo_expiry_date' => null,

                            'amc_expiry_date' => $data['amc_expiry_date'] ?? null,

                            'lan_enabled' => $data['lan_enabled'] ?? false,
                            'gst_enabled' => $data['gst_enabled'] ?? false,

                            'sales_points' => $data['sales_points'] ?? null,
                            'max_companies' => $data['max_companies'] ?? null,
                            'max_simultaneous_users' => $data['max_simultaneous_users'] ?? null,

                            'customer_code' => $data['customer_code'] ?? null,

                            // The demo key must not be used as a full licence key.
                            'license_key' => null,

                            // A full licence must be issued and activated separately.
                            'issued_at' => null,
                            'activated_at' => null,

                            // Existing installation date and motherboard serial
                            // are deliberately preserved.
                        ]);

                        // Replace the demo module configuration with full modules.
                        $license->modules()->sync($moduleIds->all());

                        // Record the conversion in the same database transaction.
                        $historyService->recordChange(
                            license: $license,
                            operationType: 'demo_converted',
                            previousConfiguration: $previousConfiguration,
                            previousLicenseKey: $previousLicenseKey,
                            remarks: 'Demo licence converted to full licence.'
                        );
                    });

                    Notification::make()
                        ->success()
                        ->title('Licence converted successfully')
                        ->body('The demo licence has been converted into a full licence.')
                        ->send();

                    // Reload the edit page with the updated licence data.
                    $this->redirect(
                        $this->getResource()::getUrl('edit', [
                            'record' => $license,
                        ])
                    );
                }),
        ];
    }
}

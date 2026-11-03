<?php

namespace App\Filament\EGJEWEL\Resources\ErpLicenses\Schemas;

use App\Models\ErpClient;
use App\Models\ErpClientBranch;
use App\Models\ErpSoftwareModule;
use App\Models\ErpSoftwareProduct;
use App\Models\ErpSoftwareVersion;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Services\LegacyV1LicenseKeyGenerator;
use Illuminate\Support\Facades\Auth;

class ErpLicenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->columns(2)
                    ->schema([
                        Select::make('erp_client_id')
                            ->label('ERP Client')
                            ->options(fn(): array => ErpClient::query()
                                ->where('is_active', true)
                                ->orderBy('company_name')
                                ->pluck('company_name', 'id')
                                ->all())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('erp_client_branch_id', null);
                            }),

                        Select::make('erp_client_branch_id')
                            ->label('Client Branch')
                            ->options(fn(Get $get): array => filled($get('erp_client_id'))
                                ? ErpClientBranch::query()
                                ->where('erp_client_id', $get('erp_client_id'))
                                ->where('is_active', true)
                                ->orderBy('branch_name')
                                ->pluck('branch_name', 'id')
                                ->all()
                                : [])
                            ->searchable()
                            ->placeholder('Select a client first')
                            ->disabled(fn(Get $get): bool => blank($get('erp_client_id'))),
                    ]),

                Section::make('Software & Edition')
                    ->columns(2)
                    ->schema([
                        Select::make('software_product_id')
                            ->label('Software Product')
                            ->options(fn(): array => ErpSoftwareProduct::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('software_version_id', null);
                                $set('modules', []);
                            }),

                        Select::make('software_version_id')
                            ->label('Software Edition')
                            ->options(fn(Get $get): array => filled($get('software_product_id'))
                                ? ErpSoftwareVersion::query()
                                ->where('software_product_id', $get('software_product_id'))
                                ->where('is_active', true)
                                ->whereBetween('legacy_edition_number', [1, 30])
                                ->orderBy('legacy_edition_number')
                                ->pluck('version_name', 'id')
                                ->all()
                                : [])
                            ->searchable()
                            ->required()
                            ->disabled(fn(Get $get): bool => blank($get('software_product_id'))),
                    ]),

                Section::make('Licence Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('license_number')
                            ->label('Licence Number')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->helperText('Can be assigned automatically in a later step.'),

                        Select::make('license_type')
                            ->options([
                                'demo' => 'Demo Licence',
                                'full' => 'Full Licence',
                            ])
                            ->default('full')
                            ->required()
                            ->live(),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'issued' => 'Issued',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'suspended' => 'Suspended',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('issued_at')
                            ->label('Issued At')
                            ->default(now()),

                        DatePicker::make('demo_expiry_date')
                            ->visible(fn(Get $get): bool => $get('license_type') === 'demo')
                            ->required(fn(Get $get): bool => $get('license_type') === 'demo'),

                        DatePicker::make('installation_date')
                            ->label('Original Installation Date'),

                        DatePicker::make('amc_expiry_date')
                            ->label('AMC Expiry Date'),

                        DateTimePicker::make('activated_at')
                            ->label('Activated At'),
                    ]),

                Section::make('Hardware Information')
                    ->schema([
                        TextInput::make('motherboard_serial')
                            ->label('Motherboard Serial Number')
                            ->maxLength(255)
                            ->helperText('Required when the licence is activated.'),
                    ]),

                Section::make('Enabled Modules')
                    ->description('Only modules belonging to the selected software product are available.')
                    ->schema([
                        Select::make('modules')
                            ->label('Software Modules')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->relationship(
                                name: 'modules',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query, Get $get): Builder => $query
                                    ->where('software_product_id', $get('software_product_id'))
                                    ->where('is_active', true),
                            )
                            ->disabled(fn(Get $get): bool => blank($get('software_product_id'))),
                    ]),

                Section::make('Legacy VFP Configuration')
                    ->columns(2)
                    ->schema([
                        Toggle::make('lan_enabled')
                            ->label('LAN Enabled')
                            ->default(false),

                        Toggle::make('gst_enabled')
                            ->label('GST Enabled')
                            ->default(false),

                        TextInput::make('sales_points')
                            ->label('Maximum Sales Points')
                            ->numeric()
                            ->integer()
                            ->minValue(1),

                        TextInput::make('max_companies')
                            ->label('Maximum Companies')
                            ->numeric()
                            ->integer()
                            ->minValue(1),

                        TextInput::make('max_simultaneous_users')
                            ->label('Maximum Simultaneous Users (MLOGIN)')
                            ->numeric()
                            ->integer()
                            ->minValue(1),
                        TextInput::make('legacy_demo_id')
                            ->label('Legacy Demo ID')
                            ->numeric()
                            ->integer()
                            ->minValue(1001)
                            ->maxValue(9999)
                            ->unique(ignoreRecord: true)
                            ->helperText('Original VFP Demo ID. Required for demo key generation.'),

                        TextInput::make('legacy_software_id')
                            ->label('Legacy Software ID')
                            ->maxLength(6)
                            ->minLength(6)
                            ->unique(ignoreRecord: true)
                            ->helperText('Six-character Software ID, for example EG3562. Required for installation key generation.'),

                        TextInput::make('customer_code')
                            ->label('Customer Code (CRCODE)')
                            ->maxLength(50),
                        TextInput::make('license_key')
                            ->label('Generated Licence Key')
                            ->disabled()
                            ->dehydrated(false)
                            ->copyable()
                            ->visible(fn($record) => filled($record?->license_key))
                            ->columnSpanFull(),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

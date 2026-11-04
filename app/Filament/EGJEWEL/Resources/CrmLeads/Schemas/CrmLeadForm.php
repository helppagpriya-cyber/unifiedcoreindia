<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Schemas;

use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrmLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Company Information
                |--------------------------------------------------------------------------
                */
                Section::make('Company Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('lead_number')
                            ->label('Lead Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Automatically generated')
                            ->visible(fn($record): bool => filled($record?->lead_number)),

                        DatePicker::make('lead_date')
                            ->label('Lead Date')
                            ->default(today())
                            ->required()
                            ->closeOnDateSelection()
                            ->displayFormat('d M Y'),

                        TextInput::make('company_name')
                            ->label('Company / Firm Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('business_type')
                            ->label('Business Type')
                            ->placeholder('Retailer, Wholesaler, Manufacturer...'),

                        TextInput::make('gst_number')
                            ->label('GST Number')
                            ->maxLength(20),

                        TextInput::make('pan_number')
                            ->label('PAN Number')
                            ->maxLength(20),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->label('City'),

                        TextInput::make('state')
                            ->label('State'),

                        TextInput::make('pincode')
                            ->label('PIN Code')
                            ->maxLength(15),

                        TextInput::make('country')
                            ->label('Country')
                            ->default('India'),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */
                Section::make('Contact Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->maxLength(255),

                        TextInput::make('mobile')
                            ->label('Mobile Number')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('alternate_mobile')
                            ->label('Alternate Mobile')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(2),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Product & Business Requirements
                |--------------------------------------------------------------------------
                */
                Section::make('Product & Business Requirements')
                    ->columns(2)
                    ->schema([
                        TextInput::make('product_interested')
                            ->label('Product Interested In')
                            ->placeholder('EGJEWEL'),

                        TextInput::make('existing_software')
                            ->label('Existing Software')
                            ->placeholder('Currently using software, if any'),

                        TextInput::make('estimated_budget')
                            ->label('Estimated Budget')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₹'),

                        Textarea::make('requirements')
                            ->label('Customer Requirements')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Lead Source & Status
                |--------------------------------------------------------------------------
                */
                Section::make('Lead Source & Status')
                    ->columns(3)
                    ->schema([
                        Select::make('source_id')
                            ->label('Lead Source')
                            ->options(fn(): array => CrmLeadSource::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),

                        Select::make('status_id')
                            ->label('Lead Status')
                            ->options(fn(): array => CrmLeadStatus::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->default(fn() => CrmLeadStatus::query()
                                ->where('slug', 'new')
                                ->value('id'))
                            ->searchable()
                            ->required(),

                        Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('normal')
                            ->required(),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Sales Executive Assignment
                |--------------------------------------------------------------------------
                */
                Section::make('Sales Executive Assignment')
                    ->columns(2)
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Assigned Executive')
                            ->options(fn(): array => User::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),

                        Select::make('created_by')
                            ->label('Created By')
                            ->options(fn(): array => User::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record): bool => filled($record)),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Follow-up Information
                |--------------------------------------------------------------------------
                */
                Section::make('Follow-up Information')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('next_follow_up_at')
                            ->label('Next Follow-up Date & Time')
                            ->seconds(false)
                            ->displayFormat('d M Y h:i A'),

                        Textarea::make('lost_reason')
                            ->label('Lost Reason')
                            ->rows(2)
                            ->visible(
                                fn($get): bool =>
                                CrmLeadStatus::query()
                                    ->whereKey($get('status_id'))
                                    ->where('is_lost', true)
                                    ->exists()
                            ),
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Internal Notes
                |--------------------------------------------------------------------------
                */
                Section::make('Internal Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

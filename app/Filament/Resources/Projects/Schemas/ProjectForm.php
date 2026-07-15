<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\BillingType;
use App\Enums\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Information')
                    ->schema([
                        Select::make('client_id')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('client_branch_id')
                            ->relationship('branch', 'branch_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('project_code')
                            ->disabled(),

                        TextInput::make('project_name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->columnSpanFull()
                    ])->columns(2),
                Section::make('Project Details')
                    ->schema([
                        TextInput::make('tender_no'),

                        TextInput::make('loa_no'),

                        TextInput::make('work_order_no'),

                        DatePicker::make('work_order_date')
                            ->native(false),

                        TextInput::make('agreement_no'),

                        TextInput::make('contract_value')
                            ->numeric()
                            ->prefix('₹'),
                    ])->columns(2),
                Section::make('Project Timeline')
                    ->schema([
                        DatePicker::make('start_date')
                            ->native(false),

                        DatePicker::make('expected_completion_date')
                            ->native(false),

                        DatePicker::make('actual_completion_date')
                            ->native(false),
                    ])->columns(2),

                Section::make('Execution Planning')
                    ->schema([
                        Select::make('billing_type')
                            ->options(BillingType::class)
                            ->default('per_page')
                            ->required(),

                        TextInput::make('estimated_boxes')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('estimated_files')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('estimated_documents')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('estimated_pages')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Select::make('status')
                            ->label('Project Status')
                            ->options(ProjectStatus::class)
                            ->default('draft')
                            ->required(),
                    ])->columns(2),


            ])->columns(1);
    }
}

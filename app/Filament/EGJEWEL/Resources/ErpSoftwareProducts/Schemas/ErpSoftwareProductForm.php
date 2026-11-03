<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareProducts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;

class ErpSoftwareProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Software Information')
                    ->description('Register and manage your software products.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('legacy_product_code')
                            ->label('Legacy Product Code')
                            ->maxLength(255)
                            ->helperText('Existing Visual FoxPro product identification, if applicable.'),

                        TextInput::make('license_profile')
                            ->default('legacy_v1')
                            ->required()
                            ->maxLength(100),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true),
                    ]),

                Section::make('Software ID Configuration')
                    ->description('Product-specific ID validation rules. Leave empty until the exact format is confirmed.')
                    ->schema([
                        KeyValue::make('software_id_format')
                            ->label('ID Format Configuration')
                            ->keyLabel('Rule')
                            ->valueLabel('Value')
                            ->addActionLabel('Add format rule')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

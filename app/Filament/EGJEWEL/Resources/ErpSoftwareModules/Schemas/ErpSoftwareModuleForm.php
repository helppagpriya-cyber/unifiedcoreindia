<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareModules\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class ErpSoftwareModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Module Information')
                    ->columns(2)
                    ->schema([
                        Select::make('software_product_id')
                            ->label('Software Product')
                            ->relationship('softwareProduct', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('legacy_flag_key')
                            ->label('Legacy VFP Flag')
                            ->maxLength(100)
                            ->helperText('Example: GIRVI, KITTY, SMS, BULL.'),

                        Select::make('legacy_group')
                            ->options([
                                1 => 'Group 1',
                                2 => 'Group 2',
                                3 => 'Group 3',
                            ])
                            ->placeholder('Not configured'),

                        Select::make('legacy_position')
                            ->options([
                                1 => 'Position 1',
                                2 => 'Position 2',
                                3 => 'Position 3',
                                4 => 'Position 4',
                            ])
                            ->placeholder('Not configured'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }
}

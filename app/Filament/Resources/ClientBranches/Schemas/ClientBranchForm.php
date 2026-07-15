<?php

namespace App\Filament\Resources\ClientBranches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientBranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([

                        Select::make('client_id')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('branch_code')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('branch_name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->schema([

                        TextInput::make('contact_person'),

                        TextInput::make('mobile')
                            ->tel(),

                        TextInput::make('email')
                            ->email(),
                    ])
                    ->columns(2),

                Section::make('Address')
                    ->schema([

                        Textarea::make('address')
                            ->columnSpanFull(),

                        TextInput::make('city'),

                        TextInput::make('state'),

                        TextInput::make('pincode'),
                    ])
                    ->columns(2),

                Section::make('Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ])->columns(1);
    }
}

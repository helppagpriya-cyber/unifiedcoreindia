<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ErpClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('client_code')
                            ->label('Client Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('company_name')
                            ->label('Company / Business Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('contact_person')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('alternate_phone')
                            ->label('Alternate Phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('gst_number')
                            ->label('GST Number')
                            ->maxLength(20),

                        TextInput::make('pan_number')
                            ->label('PAN Number')
                            ->maxLength(20),
                    ])->columnSpanFull(),

                Section::make('Business Address')
                    ->columns(3)
                    ->schema([
                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->maxLength(255),

                        TextInput::make('state')
                            ->maxLength(255),

                        TextInput::make('pincode')
                            ->label('PIN Code')
                            ->maxLength(15),

                        TextInput::make('country')
                            ->maxLength(255),
                    ])->columnSpanFull(),

                Section::make('Other Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3),

                        Toggle::make('is_active')
                            ->label('Active Client')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }
}

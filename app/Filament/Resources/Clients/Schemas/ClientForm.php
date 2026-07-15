<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\ClientType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([

                        TextInput::make('client_code')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),

                        Select::make('client_type')
                            ->options(
                                collect(ClientType::cases())
                                    ->mapWithKeys(fn($case) => [
                                        $case->value => $case->label()
                                    ])
                            )
                            ->required(),

                        TextInput::make('gst_no')
                            ->maxLength(20),

                        TextInput::make('website')
                            ->url(),

                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ])->columns(1);
    }
}

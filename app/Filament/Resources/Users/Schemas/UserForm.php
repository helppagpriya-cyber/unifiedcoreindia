<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([

                        TextInput::make('name')
                            ->required(),

                        TextInput::make('employee_code')
                            ->label('User Code')
                            ->placeholder('Will be auto generated')
                            ->disabled()
                            ->unique(ignoreRecord: true),


                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('mobile'),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn($operation) => $operation === 'create')
                            ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->same('password')
                            ->dehydrated(false),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->multiple(false)
                            ->required()
                            ->disabled(
                                ! auth()->user()->hasRole('Super Admin')
                            ),


                    ])
                    ->columns(2),



                Section::make('Additional Information')
                    ->schema([
                        Select::make('designation')
                            ->options([
                                'Manager' => 'Manager',
                                'Supervisor' => 'Supervisor',
                                'QC Incharge' => 'QC Incharge',
                                'Designer' => 'Designer',
                                'HR' => 'HR',
                                'Sales' => 'Sales',
                                'Support' => 'Support',
                            ]),

                        DatePicker::make('joining_date')
                            ->label('Joining Date')
                            ->required()
                            ->placeholder('Select joining date')
                            ->native(false),
                        DatePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->native(false)
                            ->default(now()),

                        Toggle::make('is_active')
                            ->default(true),

                    ])->columns(2),
            ])->columns(1);
    }
}

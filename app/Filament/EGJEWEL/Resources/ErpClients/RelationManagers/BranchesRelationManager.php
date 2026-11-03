<?php

namespace App\Filament\EGJEWEL\Resources\ErpClients\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $title = 'Client Branches';

    protected static ?string $recordTitleAttribute = 'branch_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Branch Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('branch_code')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('branch_name')
                            ->required()
                            ->maxLength(255),

                        Toggle::make('is_head_office')
                            ->label('Head Office'),

                        Toggle::make('is_active')
                            ->label('Active Branch')
                            ->default(true),

                        TextInput::make('contact_person')
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ]),

                Section::make('Branch Address')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->maxLength(255),

                        TextInput::make('state')
                            ->maxLength(255),

                        TextInput::make('pincode')
                            ->maxLength(15),

                        TextInput::make('country')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch_code')
                    ->label('Branch Code')
                    ->searchable(),

                TextColumn::make('branch_name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_head_office')
                    ->label('Head Office')
                    ->boolean(),

                TextColumn::make('contact_person'),

                TextColumn::make('phone')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

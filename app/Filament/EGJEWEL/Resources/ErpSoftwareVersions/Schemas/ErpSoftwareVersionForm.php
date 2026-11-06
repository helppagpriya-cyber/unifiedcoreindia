<?php

namespace App\Filament\EGJEWEL\Resources\ErpSoftwareVersions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class ErpSoftwareVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Version Information')
                    ->columns(2)
                    ->schema([
                        Select::make('software_product_id')
                            ->label('Software Product')
                            ->relationship('softwareProduct', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('version_name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('legacy_version_name')
                            ->label('Legacy Version Name')
                            ->maxLength(100),

                        TextInput::make('legacy_version_code')
                            ->label('Legacy Version Code')
                            ->maxLength(1)
                            ->helperText('One-character VFP version code, for example O or P.'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('package_path')
                            ->label('Version / Edition ZIP')
                            ->disk('local')
                            ->directory('erp-packages/versions')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', 'application/octet-stream'])
                            ->rules(['mimes:zip'])
                            ->maxSize(512000)
                            ->storeFileNamesIn('package_original_name')
                            ->helperText('Upload the installer package for this version or edition. ZIP only, up to 500 MB.')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }
}

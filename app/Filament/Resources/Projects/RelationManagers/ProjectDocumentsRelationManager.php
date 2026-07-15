<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;


class ProjectDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectDocuments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document Details')
                    ->schema([

                        Select::make('document_type')
                            ->options([
                                'tender' => 'Tender',
                                'loa' => 'LOA',
                                'work_order' => 'Work Order',
                                'agreement' => 'Agreement',
                                'nda' => 'NDA',
                                'amendment' => 'Amendment',
                                'invoice' => 'Invoice',
                                'other' => 'Other',
                            ])
                            ->required(),

                        TextInput::make('document_name')
                            ->label('Document Title')
                            ->default('Document#' . Str::random((5)))
                            ->required(),

                        Select::make('project_id')
                            ->relationship('project', 'project_name')
                            ->required(),

                    ])
                    ->columns(2),

                Section::make('Attachment')
                    ->schema([
                        
                         FileUpload::make('file_path')
                            ->directory('project-documents')
                            ->downloadable()
                            ->maxSize(10240) // 10MB
                            ->openable()
                            ->required(),
 */
                        Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpanFull(),
                        Hidden::make('uploaded_by')
                            ->default(auth()->id()),

                    ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('document_type')
                    ->label('Document Type'),

                TextEntry::make('project.project_name')
                    ->label('Project Title'),



                TextEntry::make('remarks')
                    ->label('Remarks'),

                TextEntry::make('uploadedBy.name')
                    ->label('Uploaded By'),

                TextEntry::make('file_path')
                    ->label('Attachment')
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('document_type')
                    ->badge(),

                TextColumn::make('project.project_name')
                    ->label('Project Title')
                    ->searchable(),


                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By'),

            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),

                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
                AttachAction::make(),
            ]);
    }
}

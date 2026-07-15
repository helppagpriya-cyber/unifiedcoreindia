<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('client.company_name')
                    ->label('Client'),
                TextEntry::make('branch.branch_name')
                    ->label('Client Branch'),

                TextEntry::make('project_code'),
                TextEntry::make('project_name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('tender_no')
                    ->placeholder('-'),
                TextEntry::make('loa_no')
                    ->placeholder('-'),
                TextEntry::make('work_order_no')
                    ->placeholder('-'),
                TextEntry::make('work_order_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('agreement_no')
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('expected_completion_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('actual_completion_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('contract_value')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('billing_type')
                    ->badge(),
                TextEntry::make('estimated_boxes')
                    ->numeric(),
                TextEntry::make('estimated_files')
                    ->numeric(),
                TextEntry::make('estimated_documents')
                    ->numeric(),
                TextEntry::make('estimated_pages')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Project $record): bool => $record->trashed()),
            ]);
    }
}

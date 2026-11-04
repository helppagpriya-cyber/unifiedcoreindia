<?php

namespace App\Filament\EGJEWEL\Resources\CrmLeads\Pages;

use App\Filament\EGJEWEL\Resources\CrmLeads\CrmLeadResource;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Session;

class ImportCrmLeads extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CrmLeadResource::class;

    protected string $view = 'filament.egjewel.resources.crm-leads.pages.import-crm-leads';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn() => collect(
                session('crm_lead_import_preview.rows', [])
            ))
            ->columns([
                TextColumn::make('row_number')
                    ->label('Excel Row')
                    ->sortable(),

                TextColumn::make('data.company_name')
                    ->label('Company')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('data.contact_person')
                    ->label('Contact Person')
                    ->placeholder('—'),

                TextColumn::make('data.mobile')
                    ->label('Mobile')
                    ->placeholder('—'),

                TextColumn::make('data.email')
                    ->label('Email')
                    ->placeholder('—'),

                TextColumn::make('data.source')
                    ->label('Source')
                    ->placeholder('—'),

                TextColumn::make('data.status')
                    ->label('Lead Status')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Import Result')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'ready' => 'Ready',
                        'invalid' => 'Invalid',
                        'duplicate' => 'Duplicate',
                        'possible_duplicate' => 'Review Required',
                        default => 'Unknown',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'ready' => 'success',
                        'invalid' => 'danger',
                        'duplicate' => 'warning',
                        'possible_duplicate' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('duplicate_message')
                    ->label('Duplicate Details')
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('errors')
                    ->label('Validation Errors')
                    ->formatStateUsing(fn($state): string => is_array($state)
                        ? implode('; ', $state)
                        : (string) ($state ?? ''))
                    ->wrap(),
            ])
            ->defaultSort('row_number')
            ->paginated([10, 25, 50]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Leads')
                ->icon('heroicon-o-arrow-left')
                ->url(CrmLeadResource::getUrl('index')),
        ];
    }
}

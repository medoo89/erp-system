<?php

namespace App\Filament\Resources\ProjectContractDocuments\Schemas;

use App\Models\Project;
use App\Models\ProjectContract;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectContractDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Classification')
                ->columns(3)
                ->schema([
                    Select::make('project_contract_id')
                        ->label('Contract / Counter')
                        ->options(function (): array {
                            return ProjectContract::query()
                                ->with('project')
                                ->orderByDesc('id')
                                ->get()
                                ->mapWithKeys(function (ProjectContract $contract): array {
                                    $projectName = $contract->project?->project_name
                                        ?? $contract->project?->name
                                        ?? ('Project #' . $contract->project_id);

                                    $title = $contract->title
                                        ?? $contract->contract_no
                                        ?? ('Contract #' . $contract->id);

                                    return [$contract->id => "{$projectName} — {$title}"];
                                })
                                ->toArray();
                        })
                        ->default(fn () => request()->query('project_contract_id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateHydrated(function ($state, callable $set): void {
                            $contractId = $state ?: request()->query('project_contract_id');

                            if (! $contractId) {
                                return;
                            }

                            $contract = ProjectContract::find($contractId);

                            if (! $contract) {
                                return;
                            }

                            $set('project_contract_id', $contract->id);
                            $set('project_id', $contract->project_id);
                            $set('client_id', $contract->client_id);
                        })
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $contract = $state ? ProjectContract::find($state) : null;

                            if (! $contract) {
                                return;
                            }

                            $set('project_id', $contract->project_id);
                            $set('client_id', $contract->client_id);
                        }),

                    Hidden::make('project_id'),
                    Hidden::make('client_id'),

                    Select::make('document_type')
                        ->label('Document Type')
                        ->options([
                            'agreement' => 'Agreement',
                            'order' => 'Order',
                            'amendment' => 'Amendment',
                            'renewal' => 'Renewal',
                            'tax_receipt' => 'Tax Receipt',
                            'client_approval' => 'Client Approval',
                            'other' => 'Other',
                        ])
                        ->default('agreement')
                        ->required(),

                    TextInput::make('title')
                        ->label('Title')
                        ->maxLength(255)
                        ->placeholder('Agreement / Order / Tax Receipt'),

                    TextInput::make('document_no')
                        ->label('Document No.')
                        ->maxLength(100)
                        ->placeholder('Agreement No. / Order No.'),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->default(0),

                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'LYD' => 'LYD',
                            'GBP' => 'GBP',
                        ])
                        ->default('LYD'),

                    DatePicker::make('issue_date')
                        ->label('Issue Date'),

                    DatePicker::make('expiry_date')
                        ->label('Expiry Date'),

                    Select::make('is_active')
                        ->label('Active')
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ])
                        ->default(1),
                ]),

            Section::make('Files')
                ->description('Upload one or multiple files for this document section.')
                ->schema([
                    FileUpload::make('file_paths')
                        ->label('Files')
                        ->multiple()
                        ->directory('project-contract-documents')
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames()
                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

<?php

namespace App\Filament\Resources\ProjectContracts\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contract Information')
                ->columns(3)
                ->schema([
                    Select::make('project_id')
                        ->label('Project')
                        ->options(function () {
                            return Project::query()
                                ->orderBy('id')
                                ->get()
                                ->mapWithKeys(function ($project) {
                                    $name = $project->project_name
                                        ?? $project->name
                                        ?? $project->title
                                        ?? ('Project #' . $project->id);

                                    $code = $project->project_code
                                        ?? $project->code
                                        ?? null;

                                    return [
                                        $project->id => $code ? "{$name} — {$code}" : $name,
                                    ];
                                })
                                ->toArray();
                        })
                        ->default(fn () => request()->query('project_id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateHydrated(function ($state, callable $set) {
                            $projectId = $state ?: request()->query('project_id');
                            $project = $projectId ? Project::find($projectId) : null;

                            if ($project) {
                                $set('project_id', $project->id);
                                $set('client_id', $project->client_id);
                            }
                        })
                        ->afterStateUpdated(function ($state, callable $set) {
                            $project = $state ? Project::find($state) : null;

                            if ($project) {
                                $set('client_id', $project->client_id);
                            }
                        }),

                    Hidden::make('client_id'),

                    TextInput::make('contract_no')
                        ->label('Contract No.')
                        ->maxLength(100)
                        ->placeholder('Example: DL-MC-001'),

                    TextInput::make('title')
                        ->label('Title')
                        ->maxLength(255)
                        ->required()
                        ->placeholder('Base Contract / Amendment'),

                    TextInput::make('version')
                        ->label('Version')
                        ->maxLength(50)
                        ->default('V1'),

                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'base_contract' => 'Base Contract',
                            'amendment' => 'Amendment',
                            'renewal' => 'Renewal',
                        ])
                        ->default('base_contract')
                        ->required(),

                    Select::make('amendment_type')
                        ->label('Amendment Type')
                        ->options([
                            'none' => 'None',
                            'value_addition' => 'Value Addition',
                            'date_extension' => 'Date Extension',
                            'mixed' => 'Value + Date',
                        ])
                        ->default('none'),

                    TextInput::make('contract_value')
                        ->label('Contract Value')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'LYD' => 'LYD',
                            'GBP' => 'GBP',
                        ])
                        ->default('EUR')
                        ->required(),

                    TextInput::make('tax_paid')
                        ->label('Tax Paid / Recorded')
                        ->numeric()
                        ->default(0),

                    Select::make('tax_currency')
                        ->label('Tax Currency')
                        ->options([
                            'LYD' => 'LYD',
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                        ])
                        ->default('LYD')
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Start Date'),

                    DatePicker::make('end_date')
                        ->label('End Date'),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'active' => 'Active',
                            'expired' => 'Expired',
                            'closed' => 'Closed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('active')
                        ->required(),

                    Select::make('is_active')
                        ->label('Active')
                        ->options([
                            1 => 'Yes',
                            0 => 'No',
                        ])
                        ->default(1),
                ]),

            Section::make('File & Notes')
                ->columns(1)
                ->schema([
                    FileUpload::make('file_path')
                        ->label('Contract File')
                        ->directory('project-contracts')
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames(),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

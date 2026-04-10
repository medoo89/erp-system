<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Models\JobApplicationTemplate;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job Details')
                    ->schema([
                        TextInput::make('title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('department')
                            ->label('Department')
                            ->maxLength(255),

                        Select::make('project_id')
                            ->label('Project')
                            ->options(
                                Project::query()
                                    ->with('client')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (Project $project) => [
                                        $project->id => ($project->client?->name ? $project->client->name . ' / ' : '') . $project->name,
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('client_display', null);
                                    $set('location', null);

                                    return;
                                }

                                $project = Project::with('client')->find($state);

                                if (! $project) {
                                    $set('client_display', null);

                                    return;
                                }

                                $set('client_display', $project->client?->name ?: '-');

                                if (filled($project->location)) {
                                    $set('location', $project->location);
                                }
                            })
                            ->required(),

                        TextInput::make('client_display')
                            ->label('Client')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($state, $record) {
                                if (filled($state)) {
                                    return $state;
                                }

                                return $record?->project?->client?->name ?: '-';
                            }),

                        TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),

                        Select::make('employment_type')
                            ->label('Employment Type')
                            ->required()
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'contract' => 'Contract',
                                'temporary' => 'Temporary',
                            ])
                            ->native(false),

                        Select::make('template_id')
                            ->label('Application Template')
                            ->options(
                                JobApplicationTemplate::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_active')
                            ->label('Published')
                            ->default(true),

                        DatePicker::make('closing_date')
                            ->label('Expiry Date'),
                    ])
                    ->columns(2),

                Section::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(8)
                            ->columnSpanFull(),

                        Textarea::make('requirements')
                            ->label('Requirements')
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
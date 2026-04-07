<?php

namespace App\Filament\Resources\JobApplicationFields\Schemas;

use App\Models\Job;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class JobApplicationFieldForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application Field Details')
                    ->schema([
                        Forms\Components\Toggle::make('is_global')
                            ->label('Global Field')
                            ->default(true)
                            ->live(),

                        Forms\Components\Select::make('job_id')
                            ->label('Job')
                            ->options(Job::query()->orderBy('title')->pluck('title', 'id')->toArray())
                            ->searchable()
                            ->visible(fn ($get) => ! $get('is_global'))
                            ->required(fn ($get) => ! $get('is_global')),

                        Forms\Components\TextInput::make('label')
                            ->label('Field Label')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (blank($get('field_key')) && filled($state)) {
                                    $set('field_key', str($state)->snake()->lower()->toString());
                                }
                            }),

                        Forms\Components\TextInput::make('field_key')
                            ->label('Field Key')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Auto-generated from label. Example: phone_number'),

                        Forms\Components\Select::make('field_type')
                            ->label('Field Type')
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'number' => 'Number',
                                'date' => 'Date',
                                'file' => 'File Upload',
                                'select' => 'Dropdown',
                                'checkbox' => 'Checkbox',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('field_group')
                            ->label('Field Group')
                            ->options([
                                'basic' => 'Basic',
                                'additional' => 'Additional',
                            ])
                            ->default('additional')
                            ->required(),

                        Forms\Components\TextInput::make('placeholder')
                            ->label('Placeholder')
                            ->maxLength(255)
                            ->visible(fn ($get) => in_array($get('field_type'), [
                                'text',
                                'textarea',
                                'number',
                            ])),

                        Forms\Components\Textarea::make('help_text')
                            ->label('Help Text')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Hidden::make('sort_order')
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
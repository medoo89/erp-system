<?php

namespace App\Filament\Resources\JobApplicationFields\Schemas;

use App\Models\Job;
use App\Models\JobApplicationField;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->options(JobApplicationField::fieldTypeOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if (! in_array($state, [
                                    JobApplicationField::TYPE_SELECT,
                                    JobApplicationField::TYPE_CHECKBOX,
                                    JobApplicationField::TYPE_MULTI_CHECKBOX,
                                ], true)) {
                                    $set('options', []);
                                }
                            })
                            ->helperText('Checkbox = one selected option. Multi Checkbox = multiple selected options. Dropdown = one selected option.'),

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
                                JobApplicationField::TYPE_TEXT,
                                JobApplicationField::TYPE_TEXTAREA,
                                JobApplicationField::TYPE_NUMBER,
                            ], true)),

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

                Section::make('Field Options')
                    ->description('Add the choices that should appear to the applicant. This section appears only for Dropdown, Checkbox, and Multi Checkbox fields.')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship('options')
                            ->label('Options')
                            ->schema([
                                Forms\Components\TextInput::make('option_label')
                                    ->label('Option Label')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                        if (blank($get('option_value')) && filled($state)) {
                                            $set('option_value', str($state)->snake()->lower()->toString());
                                        }
                                    }),

                                Forms\Components\TextInput::make('option_value')
                                    ->label('Option Value')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from label. You can edit it if needed.'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Add Option')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($get) => in_array($get('field_type'), [
                        JobApplicationField::TYPE_SELECT,
                        JobApplicationField::TYPE_CHECKBOX,
                        JobApplicationField::TYPE_MULTI_CHECKBOX,
                    ], true))
                    ->columns(1),
            ]);
    }
}

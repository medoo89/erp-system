<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Information')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->required()
                            ->options(
                                Client::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Project Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Project Code')
                            ->maxLength(255),

                        TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),

                        Select::make('site_type')
                            ->label('Site Type')
                            ->options([
                                'onshore' => 'Onshore',
                                'offshore' => 'Offshore',
                                'plant' => 'Plant',
                                'office' => 'Office',
                                'workshop' => 'Workshop',
                            ])
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->default('active')
                            ->options([
                                'active' => 'Active',
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'closed' => 'Closed',
                            ])
                            ->native(false),

                        DatePicker::make('start_date')
                            ->label('Start Date'),

                        DatePicker::make('end_date')
                            ->label('End Date'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
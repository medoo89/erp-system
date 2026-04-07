<?php

namespace App\Filament\Resources\JobApplicationTemplates\Schemas;

use App\Models\JobApplicationField;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class JobApplicationTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Template Name')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),

                Section::make('Application Fields')
                    ->schema([
                        Forms\Components\CheckboxList::make('basic_fields')
                            ->label('Basic Fields')
                            ->options(
                                JobApplicationField::query()
                                    ->where('field_group', 'basic')
                                    ->where('is_active', true)
                                    ->whereNotIn('field_key', [
                                        'phone',
                                        'phone_country_code',
                                        'whatsapp_country_code',
                                    ])
                                    ->orderBy('sort_order')
                                    ->pluck('label', 'id')
                                    ->toArray()
                            )
                            ->columns(2),

                        Forms\Components\CheckboxList::make('additional_fields')
                            ->label('Additional Fields')
                            ->options(
                                JobApplicationField::query()
                                    ->where('field_group', 'additional')
                                    ->where('is_active', true)
                                    ->whereNotIn('field_key', [
                                        'phone',
                                        'phone_country_code',
                                        'whatsapp_country_code',
                                    ])
                                    ->orderBy('sort_order')
                                    ->pluck('label', 'id')
                                    ->toArray()
                            )
                            ->columns(2),
                    ]),
            ]);
    }
}
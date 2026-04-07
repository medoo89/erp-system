<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('full_name')
                    ->label('Full Name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->label('Phone'),

                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'screening' => 'Screening',
                        'under_review' => 'Under Review',
                        'shortlisted' => 'Shortlisted',
                        'client_submitted' => 'Client Submitted',
                        'interview' => 'Interview',
                        'interview_scheduled' => 'Interview Scheduled',
                        'approved' => 'Approved',
                        'hired' => 'Hired',
                        'qualified' => 'Qualified',
                        'on_hold' => 'On Hold',
                        'rejected' => 'Rejected',
                        'declined' => 'Declined',
                    ])
                    ->default('new')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(4),

                Section::make('Application Answers')
                    ->schema(function ($record) {
                        if (! $record || ! $record->job) {
                            return [];
                        }

                        $template = $record->job->template;

                        if (! $template) {
                            return [];
                        }

                        $fields = $template->fields()
                            ->where('is_active', true)
                            ->get();

                        return $fields->map(function ($field) use ($record) {
                            $storedValue = $record->values()
                                ->where('field_id', $field->id)
                                ->first();

                            $value = $storedValue?->value;

                            if ($field->field_type === 'file') {
                                if (! $value) {
                                    return Forms\Components\Placeholder::make('field_' . $field->id)
                                        ->label($field->label)
                                        ->content('-');
                                }

                                return Forms\Components\Placeholder::make('field_' . $field->id)
                                    ->label($field->label)
                                    ->content(new HtmlString(
                                        '<a href="' . asset('storage/' . $value) . '" target="_blank">Open file</a>'
                                    ));
                            }

                            if ($field->field_type === 'select' && $value) {
                                $option = $field->options()
                                    ->where('option_value', $value)
                                    ->first();

                                $displayValue = $option?->option_label ?? $value;

                                return Forms\Components\Placeholder::make('field_' . $field->id)
                                    ->label($field->label)
                                    ->content($displayValue);
                            }

                            return Forms\Components\Placeholder::make('field_' . $field->id)
                                ->label($field->label)
                                ->content($value ?: '-');
                        })->toArray();
                    }),
            ]);
    }
}
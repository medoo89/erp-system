<?php

namespace App\Filament\Resources\PreEmployments\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PreEmploymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->schema([
                        Placeholder::make('overview_candidate')
                            ->label('Candidate')
                            ->content(fn ($record) => $record?->candidate_name ?: '-'),

                        Placeholder::make('overview_position')
                            ->label('Position')
                            ->content(fn ($record) => $record?->job?->title ?: '-'),

                        Placeholder::make('overview_project')
                            ->label('Project')
                            ->content(fn ($record) => $record?->job?->project?->name ?: '-'),

                        Placeholder::make('overview_client')
                            ->label('Client')
                            ->content(fn ($record) => $record?->job?->project?->client?->name ?: '-'),

                        Placeholder::make('overview_status')
                            ->label('Current Status')
                            ->content(fn ($record) => self::label($record?->status)),

                        Placeholder::make('overview_officer')
                            ->label('Operation Officer')
                            ->content(fn ($record) => $record?->assignedHrUser?->name ?: '-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Process Control')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->default('initiated')
                            ->options([
                                'initiated' => 'Initiated',
                                'under_preparation' => 'Under Preparation',
                                'awaiting_candidate_upload' => 'Awaiting Candidate Upload',
                                'documents_under_review' => 'Documents Under Review',
                                'additional_documents_required' => 'Additional Documents Required',
                                'pending_medical' => 'Pending Medical',
                                'pending_visa' => 'Pending Visa',
                                'pending_travel' => 'Pending Travel',
                                'ready_for_employment' => 'Ready for Employment',
                                'converted_to_employment' => 'Converted to Employment',
                                'declined' => 'Declined',
                            ])
                            ->native(false),

                        Select::make('assigned_hr_user_id')
                            ->label('Operation Officer')
                            ->options(
                                User::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Placeholder::make('portal_link_box')
                            ->label('Public Link')
                            ->content(function ($record) {
                                if (! $record?->portal_token) {
                                    return new HtmlString('<span style="color:#64748b;">Will be available after save.</span>');
                                }

                                $url = url('/pre-employment/portal/' . $record->portal_token);
                                $escapedUrl = e($url);

                                return new HtmlString(
                                    '<div style="
                                        border:1px solid #dbe3ee;
                                        background:#f8fafc;
                                        border-radius:14px;
                                        padding:14px;
                                        line-height:1.6;
                                    ">
                                        <div style="
                                            font-size:12px;
                                            color:#64748b;
                                            margin-bottom:8px;
                                            font-weight:600;
                                        ">Candidate portal link</div>
                                        <div style="
                                            word-break:break-word;
                                            font-size:14px;
                                            color:#0f172a;
                                        ">' . $escapedUrl . '</div>
                                    </div>'
                                );
                            })
                            ->columnSpanFull(),

                        Placeholder::make('portal_last_sent_at_display')
                            ->label('Last Sent')
                            ->content(fn ($record) => $record?->portal_last_sent_at?->format('M j, Y H:i') ?: '-'),

                        Placeholder::make('portal_last_submitted_at_display')
                            ->label('Last Submitted')
                            ->content(fn ($record) => $record?->portal_last_submitted_at?->format('M j, Y H:i') ?: '-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Client Tracking')
                    ->schema([
                        Select::make('contract_status')
                            ->label('Contract Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'under_discussion' => 'Under Discussion',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'signed' => 'Signed',
                            ])
                            ->native(false),

                        Select::make('caf_status')
                            ->label('CAF Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'received_signed' => 'Received Signed',
                                'approved' => 'Approved',
                            ])
                            ->native(false),

                        FileUpload::make('caf_file_path')
                            ->label('CAF File')
                            ->disk('public')
                            ->directory(fn ($record) => 'pre-employment/' . ($record?->id ?: 'draft') . '/client')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/png',
                                'image/jpeg',
                                'image/jpg',
                            ]),

                        Select::make('gl_status')
                            ->label('General Letter Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'received_signed' => 'Received Signed',
                                'approved' => 'Approved',
                            ])
                            ->native(false),

                        FileUpload::make('gl_file_path')
                            ->label('General Letter File')
                            ->disk('public')
                            ->directory(fn ($record) => 'pre-employment/' . ($record?->id ?: 'draft') . '/client')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/png',
                                'image/jpeg',
                                'image/jpg',
                            ]),

                        Textarea::make('client_tracking_notes')
                            ->label('Client Tracking Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Candidate Tracking')
                    ->schema([
                        Select::make('medical_status')
                            ->label('Medical Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'fit' => 'Fit',
                                'not_fit' => 'Not Fit',
                            ])
                            ->native(false),

                        Select::make('visa_status')
                            ->label('Visa Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                            ])
                            ->native(false),

                        Select::make('travel_status')
                            ->label('Travel Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'booked' => 'Booked',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->native(false),

                        DatePicker::make('availability_date')
                            ->label('Availability Date'),

                        Textarea::make('candidate_tracking_notes')
                            ->label('Candidate Tracking Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Commercial')
                    ->schema([
                        TextInput::make('expected_rate')
                            ->label('Expected Rate / Salary')
                            ->maxLength(255),

                        TextInput::make('final_rate')
                            ->label('Final Approved Rate / Salary')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->visible(fn () => self::canSeeCommercial())
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Candidate / Process Notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function label(?string $value): string
    {
        return match ($value) {
            'initiated' => 'Initiated',
            'under_preparation' => 'Under Preparation',
            'awaiting_candidate_upload' => 'Awaiting Candidate Upload',
            'documents_under_review' => 'Documents Under Review',
            'additional_documents_required' => 'Additional Documents Required',
            'pending_medical' => 'Pending Medical',
            'pending_visa' => 'Pending Visa',
            'pending_travel' => 'Pending Travel',
            'ready_for_employment' => 'Ready for Employment',
            'converted_to_employment' => 'Converted to Employment',
            'declined' => 'Declined',
            default => $value ? ucfirst(str_replace('_', ' ', $value)) : '-',
        };
    }

    protected static function canSeeCommercial(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole(['admin', 'finance']);
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('finance');
        }

        return true;
    }
}
<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    protected static ?string $title = 'Files & Documents';

    protected static ?string $modelLabel = 'File';

    protected static ?string $pluralModelLabel = 'Files & Documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('File Title')
                    ->required()
                    ->maxLength(255),

                Select::make('category')
                    ->label('Category')
                    ->options([
                        'passport' => 'Passport',
                        'visa' => 'Visa',
                        'medical' => 'Medical',
                        'personal_photo' => 'Personal Photo',
                        'certificate' => 'Certificate',
                        'contract' => 'Contract',
                        'rotation_document' => 'Rotation Document',
                        'travel_request' => 'Travel Request',
                        'ticket' => 'Ticket',
                        'internal_document' => 'Internal Document',
                        'other' => 'Other',
                    ])
                    ->native(false)
                    ->searchable()
                    ->live(),

                Select::make('document_status')
                    ->label('Document Status')
                    ->options(function (callable $get) {
                        return match ($get('category')) {
                            'visa' => [
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'renewed' => 'Renewed',
                                'expired' => 'Expired',
                                'rejected' => 'Rejected',
                            ],
                            'medical' => [
                                'pending' => 'Pending',
                                'fit' => 'Fit',
                                'not_fit' => 'Not Fit',
                                'renewed' => 'Renewed',
                                'expired' => 'Expired',
                            ],
                            'contract' => [
                                'active' => 'Active',
                                'renewal_in_progress' => 'Renewal In Progress',
                                'renewed' => 'Renewed',
                                'completed' => 'Completed',
                                'terminated' => 'Terminated',
                            ],
                            'travel_request' => [
                                'pending_request' => 'Pending Request',
                                'request_received' => 'Request Received',
                            ],
                            'ticket' => [
                                'ticket_booked' => 'Ticket Booked',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ],
                            default => [],
                        };
                    })
                    ->native(false)
                    ->visible(fn (callable $get) => in_array($get('category'), ['visa', 'medical', 'contract', 'travel_request', 'ticket'], true)),

                Toggle::make('apply_to_current_rotation')
                    ->label('Apply to Current Rotation')
                    ->default(false)
                    ->visible(fn (callable $get) => in_array($get('category'), ['travel_request', 'ticket'], true)),

                DatePicker::make('document_date')
                    ->label('Document Date'),

                DatePicker::make('expiry_date')
                    ->label('Expiry Date'),

                FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory(fn () => 'employment-files/' . ($this->ownerRecord?->id ?? 'draft'))
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                    ])
                    ->maxSize(20480)
                    ->helperText('Allowed: PDF, Images, Word, Excel, CSV only.'),

                Select::make('uploaded_by_type')
                    ->label('Submitted By')
                    ->options([
                        'candidate' => 'Candidate',
                        'admin' => 'Admin',
                    ])
                    ->default('admin')
                    ->native(false)
                    ->required(),

                Toggle::make('is_current')
                    ->label('Mark as Current Version')
                    ->default(true),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Document')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(function ($state, $record) {
                        $icon = match ($record->category) {
                            'passport' => '🛂',
                            'visa' => '🛃',
                            'medical' => '🩺',
                            'personal_photo' => '🖼️',
                            'certificate' => '📜',
                            'contract' => '📄',
                            'rotation_document' => '🔁',
                            'travel_request' => '✈️',
                            'ticket' => '🎫',
                            'internal_document' => '🗂️',
                            default => '📁',
                        };

                        return $icon . ' ' . ($state ?: '-');
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ($state) {
                        'passport' => 'info',
                        'visa' => 'warning',
                        'medical' => 'success',
                        'personal_photo' => 'gray',
                        'certificate' => 'primary',
                        'contract' => 'success',
                        'rotation_document' => 'warning',
                        'travel_request' => 'info',
                        'ticket' => 'warning',
                        'internal_document' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('document_status')
                    ->label('Doc Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('version_no')
                    ->label('Version')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => 'V' . $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_current')
                    ->label('Current')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Current' : 'Old')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('uploaded_by_type')
                    ->label('Submitted By')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'candidate' => 'Candidate',
                        'admin' => 'Admin',
                        default => '-',
                    })
                    ->color(fn ($state) => $state === 'candidate' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('open_file')
                    ->label('File')
                    ->state(function ($record) {
                        if (! filled($record->file_path)) {
                            return '-';
                        }

                        $extension = strtolower(pathinfo($record->file_path, PATHINFO_EXTENSION));

                        return match ($extension) {
                            'pdf' => 'Open PDF',
                            'jpg', 'jpeg', 'png', 'webp' => 'Open Image',
                            'doc', 'docx' => 'Open Document',
                            'xls', 'xlsx', 'csv' => 'Open Sheet',
                            default => 'Download File',
                        };
                    })
                    ->url(fn ($record) => filled($record->file_path) ? Storage::disk('public')->url($record->file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('document_date')
                    ->label('Document Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('M j, Y') : '-')
                    ->badge()
                    ->color(function ($state) {
                        if (! $state) {
                            return 'gray';
                        }

                        $date = Carbon::parse($state);

                        if ($date->isPast()) {
                            return 'danger';
                        }

                        if ($date->diffInDays(now()) <= 30) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('apply_to_current_rotation')
                    ->label('To Rotation')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Upload File')
                    ->modalHeading('Upload File / Document')
                    ->modalSubmitActionLabel('Upload')
                    ->requiresConfirmation()
                    ->mutateDataUsing(function (array $data): array {
                        $data['uploaded_by_user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('File uploaded successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->requiresConfirmation()
                    ->modalHeading('Edit File')
                    ->modalSubmitActionLabel('Save Changes'),

                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->modalHeading('Delete file')
                    ->modalDescription('Are you sure you want to delete this file record?')
                    ->modalSubmitActionLabel('Yes, Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
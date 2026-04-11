<?php

namespace App\Filament\Resources\PreEmployments\RelationManagers;

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
                        'caf' => 'CAF',
                        'gl' => 'General Letter',
                        'contract' => 'Contract',
                        'candidate_upload' => 'Candidate Upload',
                        'internal_document' => 'Internal Document',
                        'other' => 'Other',
                    ])
                    ->searchable()
                    ->native(false),

                DatePicker::make('document_date')
                    ->label('Document Date'),

                DatePicker::make('expiry_date')
                    ->label('Expiry Date'),

                FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory(fn ($record) => 'pre-employment-files/' . ($this->ownerRecord?->id ?? 'draft'))
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]),

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
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn (?string $state): string => match ($state) {
                        'passport' => 'info',
                        'visa' => 'warning',
                        'medical' => 'success',
                        'personal_photo' => 'gray',
                        'certificate' => 'primary',
                        'caf' => 'warning',
                        'gl' => 'info',
                        'contract' => 'success',
                        'candidate_upload' => 'info',
                        'internal_document' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('version_no')
                    ->label('Version')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => 'V' . $state)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),

                Tables\Columns\TextColumn::make('uploaded_by_type')
                    ->label('Submitted By')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'candidate' => 'Candidate',
                        'admin' => 'Admin',
                        default => '-',
                    })
                    ->color(fn (?string $state) => $state === 'candidate' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('open_file')
                    ->label('File')
                    ->state(fn ($record) => filled($record->file_path) ? 'Open File' : '-')
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
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return '-';
                        }

                        return Carbon::parse($state)->format('M j, Y');
                    })
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
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
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
                    ->modalHeading('Edit File')
                    ->modalSubmitActionLabel('Save Changes')
                    ->requiresConfirmation(),

                DeleteAction::make()
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
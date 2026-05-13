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
use Illuminate\Database\Eloquent\Builder;
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
                        'cv' => 'CV',
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
                        'cv' => 'primary',
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

                Tables\Columns\TextColumn::make('is_current')
                    ->label('Version Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Current' : 'Old Version')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

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
            ->filters([
                Tables\Filters\SelectFilter::make('version_view')
                    ->label('Version View')
                    ->options([
                        'current' => 'Current Files Only',
                        'all' => 'All Versions',
                        'old' => 'Old Versions Only',
                    ])
                    ->default('current')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? 'current') {
                            'all' => $query->withoutGlobalScopes(),
                            'old' => $query->where('is_current', false),
                            default => $query->where('is_current', true),
                        };
                    }),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'cv' => 'CV',
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
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'upload_file'))
                    ->label('Upload File')
                    ->modalHeading('Upload File / Document')
                    ->modalSubmitActionLabel('Upload')
                    ->requiresConfirmation()
                    ->mutateDataUsing(function (array $data): array {
                        $data['uploaded_by_user_id'] = auth()->id();

                        $categoryText = strtolower(trim(($data['category'] ?? '') . ' ' . ($data['title'] ?? '') . ' ' . ($data['file_path'] ?? '')));

                        if (str_contains($categoryText, 'cv') || str_contains($categoryText, 'resume')) {
                            $data['category'] = 'cv';
                        }

                        if (($data['category'] ?? null) === 'cv') {
                            $candidateName = $this->ownerRecord->candidate_name
                                ?? $this->ownerRecord->full_name
                                ?? $this->ownerRecord->name
                                ?? 'Candidate';

                            $data['title'] = trim($candidateName) . ' CV';
                        }

                        $data['is_current'] = (bool) ($data['is_current'] ?? true);

                        return $data;
                    })
                    ->before(function (array $data): void {
                        if (($data['is_current'] ?? true) && filled($data['category'] ?? null)) {
                            $this->ownerRecord->files()
                                ->where('category', $data['category'])
                                ->where('is_current', true)
                                ->update(['is_current' => false]);
                        }
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
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'upload_file'))
                    ->modalHeading('Edit File')
                    ->modalSubmitActionLabel('Save Changes')
                    ->requiresConfirmation()
                    ->mutateDataUsing(function (array $data): array {
                        $categoryText = strtolower(trim(($data['category'] ?? '') . ' ' . ($data['title'] ?? '') . ' ' . ($data['file_path'] ?? '')));

                        if (str_contains($categoryText, 'cv') || str_contains($categoryText, 'resume')) {
                            $data['category'] = 'cv';
                        }

                        if (($data['category'] ?? null) === 'cv') {
                            $candidateName = $this->ownerRecord->candidate_name
                                ?? $this->ownerRecord->full_name
                                ?? $this->ownerRecord->name
                                ?? 'Candidate';

                            $data['title'] = trim($candidateName) . ' CV';
                        }

                        return $data;
                    })
                    ->after(function ($record): void {
                        if ($record->is_current && filled($record->category)) {
                            $this->ownerRecord->files()
                                ->where('category', $record->category)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),

                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'delete_file'))
                    ->requiresConfirmation()
                    ->modalHeading('Delete file')
                    ->modalDescription('Are you sure you want to delete this file record?')
                    ->modalSubmitActionLabel('Yes, Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'delete_file'))
                        ->requiresConfirmation(),
                ]),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (
            auth()->user()?->canErp('pre_employments', 'view')
            || auth()->user()?->canErp('pre_employments', 'upload_file')
            || auth()->user()?->canErp('pre_employments', 'delete_file')
        );
    }
}

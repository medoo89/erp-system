<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use App\Services\EmploymentDocumentGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Official Documents';

    protected static ?string $modelLabel = 'Document';

    protected static ?string $pluralModelLabel = 'Official Documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('document_type')
                    ->label('Document Type')
                    ->options([
                        'caf' => 'CAF',
                        'general_letter' => 'General Letter',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('title')
                    ->label('Title')
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'generated' => 'Generated',
                        'edited' => 'Edited',
                        'final' => 'Final',
                        'sent' => 'Sent',
                        'signed' => 'Signed',
                        'received' => 'Received',
                    ])
                    ->default('draft')
                    ->required()
                    ->native(false),

                FileUpload::make('docx_file_path')
                    ->label('DOCX File')
                    ->disk('public')
                    ->directory(fn () => 'employment-documents/' . ($this->ownerRecord?->id ?? 'draft') . '/docx')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(20480),

                FileUpload::make('pdf_file_path')
                    ->label('PDF File')
                    ->disk('public')
                    ->directory(fn () => 'employment-documents/' . ($this->ownerRecord?->id ?? 'draft') . '/pdf')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                    ])
                    ->maxSize(20480),

                FileUpload::make('final_file_path')
                    ->label('Final Approved File')
                    ->disk('public')
                    ->directory(fn () => 'employment-documents/' . ($this->ownerRecord?->id ?? 'draft') . '/final')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(20480),

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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'caf' => 'CAF',
                        'general_letter' => 'General Letter',
                        default => '-',
                    })
                    ->color(fn ($state) => match ($state) {
                        'caf' => 'info',
                        'general_letter' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst($state) : '-')
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'gray',
                        'generated' => 'info',
                        'edited' => 'warning',
                        'final' => 'success',
                        'sent' => 'warning',
                        'signed' => 'success',
                        'received' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('open_docx')
                    ->label('DOCX')
                    ->state(fn ($record) => filled($record->docx_file_path) ? 'Open DOCX' : '-')
                    ->url(fn ($record) => filled($record->docx_file_path) ? Storage::disk('public')->url($record->docx_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('open_pdf')
                    ->label('PDF')
                    ->state(fn ($record) => filled($record->pdf_file_path) ? 'Open PDF' : '-')
                    ->url(fn ($record) => filled($record->pdf_file_path) ? Storage::disk('public')->url($record->pdf_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('open_final')
                    ->label('Final')
                    ->state(fn ($record) => filled($record->final_file_path) ? 'Open Final' : '-')
                    ->url(fn ($record) => filled($record->final_file_path) ? Storage::disk('public')->url($record->final_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Generated At')
                    ->dateTime('M j, Y H:i')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('generateGeneralLetter')
                    ->label(fn () => $this->ownerRecord->documents()->where('document_type', 'general_letter')->exists()
                        ? 'Regenerate General Letter'
                        : 'Generate General Letter')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate General Letter')
                    ->modalDescription('This will create or overwrite the employee General Letter and keep only one active record for this type.')
                    ->modalSubmitActionLabel('Generate')
                    ->action(function () {
                        $document = app(EmploymentDocumentGenerator::class)
                            ->generateGeneralLetter($this->ownerRecord, auth()->user());

                        Notification::make()
                            ->title('General Letter generated successfully')
                            ->body('Reference: ' . $document->reference)
                            ->success()
                            ->send();
                    }),

                Action::make('generateCaf')
                    ->label(fn () => $this->ownerRecord->documents()->where('document_type', 'caf')->exists()
                        ? 'Regenerate CAF'
                        : 'Generate CAF')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Generate CAF')
                    ->modalDescription('This will create or overwrite the employee CAF and keep only one active record for this type.')
                    ->modalSubmitActionLabel('Generate')
                    ->action(function () {
                        $document = app(EmploymentDocumentGenerator::class)
                            ->generateCaf($this->ownerRecord, auth()->user());

                        Notification::make()
                            ->title('CAF generated successfully')
                            ->body('Reference: ' . $document->reference)
                            ->success()
                            ->send();
                    }),

                CreateAction::make()
                    ->label('Add Official Document')
                    ->requiresConfirmation()
                    ->mutateDataUsing(function (array $data): array {
                        $data['generated_by_user_id'] = auth()->id();

                        if (($data['status'] ?? null) === 'generated') {
                            $data['generated_at'] = now();
                        }

                        return $data;
                    })
                    ->after(fn () => Notification::make()->title('Document created successfully')->success()->send()),
            ])
            ->recordActions([
                EditAction::make()
                    ->requiresConfirmation(),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }
}
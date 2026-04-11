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
use Illuminate\Support\Facades\Storage;

class RotationsRelationManager extends RelationManager
{
    protected static string $relationship = 'rotations';

    protected static ?string $title = 'Rotation History';

    protected static ?string $modelLabel = 'Rotation';

    protected static ?string $pluralModelLabel = 'Rotation History';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rotation_label')
                    ->label('Rotation Label')
                    ->maxLength(255)
                    ->placeholder('Example: Rotation 01 / April Offshore'),

                Select::make('status')
                    ->label('Rotation Status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'paused' => 'Paused',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                TextInput::make('rotation_pattern')
                    ->label('Rotation Pattern')
                    ->maxLength(255)
                    ->placeholder('28/28, 35/35 ...'),

                Select::make('travel_status')
                    ->label('Travel Status')
                    ->options([
                        'pending_request' => 'Pending Request',
                        'request_received' => 'Request Received',
                        'ticket_booked' => 'Ticket Booked',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                FileUpload::make('travel_request_file_path')
                    ->label('Travel Request File')
                    ->disk('public')
                    ->directory(fn () => 'employment-rotations/' . ($this->ownerRecord?->id ?? 'draft') . '/travel-requests')
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

                FileUpload::make('ticket_file_path')
                    ->label('Ticket File')
                    ->disk('public')
                    ->directory(fn () => 'employment-rotations/' . ($this->ownerRecord?->id ?? 'draft') . '/tickets')
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

                DatePicker::make('from_date')
                    ->label('From Date'),

                DatePicker::make('to_date')
                    ->label('To Date'),

                DatePicker::make('mobilization_date')
                    ->label('Mobilization Date'),

                DatePicker::make('demobilization_date')
                    ->label('Demobilization Date'),

                Toggle::make('is_current')
                    ->label('Mark as Current Rotation')
                    ->default(false),

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
            ->recordTitleAttribute('rotation_label')
            ->columns([
                Tables\Columns\TextColumn::make('rotation_label')
                    ->label('Rotation')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ($state) {
                        'scheduled' => 'warning',
                        'active' => 'success',
                        'completed' => 'info',
                        'paused' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('rotation_pattern')
                    ->label('Pattern')
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('travel_status')
                    ->label('Travel')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ($state) {
                        'pending_request' => 'warning',
                        'request_received' => 'info',
                        'ticket_booked' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('travel_request_link')
                    ->label('Travel Request')
                    ->state(fn ($record) => filled($record->travel_request_file_path) ? 'Open File' : '-')
                    ->url(fn ($record) => filled($record->travel_request_file_path) ? Storage::disk('public')->url($record->travel_request_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('ticket_link')
                    ->label('Ticket')
                    ->state(fn ($record) => filled($record->ticket_file_path) ? 'Open File' : '-')
                    ->url(fn ($record) => filled($record->ticket_file_path) ? Storage::disk('public')->url($record->ticket_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('from_date')
                    ->label('From')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_date')
                    ->label('To')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mobilization_date')
                    ->label('Mobilization')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('demobilization_date')
                    ->label('Demobilization')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),
            ])
            ->defaultSort('from_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Rotation')
                    ->requiresConfirmation()
                    ->modalHeading('Add Rotation')
                    ->modalSubmitActionLabel('Add Rotation')
                    ->after(fn () => Notification::make()->title('Rotation added successfully')->success()->send()),
            ])
            ->recordActions([
                EditAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Edit Rotation')
                    ->modalSubmitActionLabel('Save Changes'),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Rotation')
                    ->modalDescription('Are you sure you want to delete this rotation record?')
                    ->modalSubmitActionLabel('Yes, Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }
}
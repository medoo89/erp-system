<?php

namespace App\Filament\Resources\PreEmployments\RelationManagers;

use App\Mail\PreEmploymentPortalRequestMail;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class PortalFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'portalFields';

    protected static ?string $title = 'Candidate Requirements';

    protected static ?string $modelLabel = 'Requirement Field';

    protected static ?string $pluralModelLabel = 'Candidate Requirements';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Field Label')
                    ->required()
                    ->maxLength(255),

                TextInput::make('field_key')
                    ->label('Field Key')
                    ->helperText('Optional. Leave empty to generate automatically.')
                    ->maxLength(255),

                Select::make('field_type')
                    ->label('Field Type')
                    ->required()
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'date' => 'Date',
                        'number' => 'Number',
                        'email' => 'Email',
                        'file' => 'File Upload',
                    ])
                    ->default('text')
                    ->native(false)
                    ->live(),

                Select::make('document_category')
                    ->label('Document Category')
                    ->options([
                        'passport' => 'Passport',
                        'cv' => 'CV',
                        'personal_photo' => 'Personal Photo',
                        'visa' => 'Visa',
                        'medical' => 'Medical',
                        'certificate' => 'Certificate',
                        'contract' => 'Contract',
                        'candidate_upload' => 'Candidate Upload',
                        'other' => 'Other',
                    ])
                    ->native(false)
                    ->visible(fn (callable $get) => $get('field_type') === 'file')
                    ->helperText('Use this to keep re-uploads under the same internal document type.'),

                Textarea::make('instructions')
                    ->label('Instructions for Candidate')
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('is_required')
                    ->label('Required')
                    ->default(false),

                Toggle::make('visible_to_candidate')
                    ->label('Visible to Candidate')
                    ->helperText('Turn off only for internal-only fields.')
                    ->default(true),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Placeholder::make('sort_info')
                    ->label('Order')
                    ->content('Order is assigned automatically.')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Field')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('field_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'date' => 'Date',
                        'number' => 'Number',
                        'email' => 'Email',
                        'file' => 'File Upload',
                        default => $state ?: '-',
                    })
                    ->color('info'),

                Tables\Columns\TextColumn::make('document_category')
                    ->label('Document Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                Tables\Columns\IconColumn::make('visible_to_candidate')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Field')
                    ->modalHeading('Add Candidate Requirement')
                    ->modalSubmitActionLabel('Add Field')
                    ->after(function () {
                        Notification::make()
                            ->title('Field added successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('sendRequirementsEmail')
                    ->label('Send Requirements Email')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send requirements email')
                    ->modalDescription('This will email the candidate and ask them to review and complete the required items in the portal.')
                    ->modalSubmitActionLabel('Yes, Send')
                    ->disabled(fn () => blank($this->ownerRecord?->candidate_email) || blank($this->ownerRecord?->portal_token))
                    ->action(function () {
                        Mail::to($this->ownerRecord->candidate_email)
                            ->send(new PreEmploymentPortalRequestMail($this->ownerRecord, true));

                        $this->ownerRecord->update([
                            'portal_last_sent_at' => now(),
                            'status' => in_array($this->ownerRecord->status, ['initiated', 'under_preparation'], true)
                                ? 'awaiting_candidate_upload'
                                : $this->ownerRecord->status,
                        ]);

                        Notification::make()
                            ->title('Requirements email sent successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Candidate Requirement')
                    ->modalSubmitActionLabel('Save Changes')
                    ->requiresConfirmation(),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
<?php

namespace App\Filament\Resources\SalarySlips\RelationManagers;

use App\Models\SalarySlipAttachment;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Attachments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('attachment_type')
                ->label('Attachment Type')
                ->options(SalarySlipAttachment::typeLabels())
                ->required()
                ->native(false),

            TextInput::make('title')
                ->maxLength(255),

            FileUpload::make('file_path')
                ->label('File')
                ->directory('salary-slip-attachments')
                ->disk('public')
                ->required()
                ->downloadable()
                ->openable()
                ->previewable(false),

            Textarea::make('notes')
                ->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('attachment_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => SalarySlipAttachment::typeLabels()[$state] ?? $state)
                    ->badge(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Original Name')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'upload_attachment'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $path = $data['file_path'] ?? null;

                        $data['original_name'] = filled($path) ? basename((string) $path) : null;
                        $data['mime_type'] = null;
                        $data['size_bytes'] = null;
                        $data['uploaded_by'] = auth()->id();

                        if (blank($data['title'] ?? null) && filled($path)) {
                            $data['title'] = basename((string) $path);
                        }

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'upload_attachment'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $path = $data['file_path'] ?? null;

                        if (blank($data['title'] ?? null) && filled($path)) {
                            $data['title'] = basename((string) $path);
                        }

                        $data['original_name'] = filled($path) ? basename((string) $path) : ($data['original_name'] ?? null);

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'delete')),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }
}

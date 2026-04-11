<?php

namespace App\Filament\Resources\PreEmployments\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortalValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'portalValues';

    protected static ?string $title = 'Candidate Submissions';

    protected static ?string $modelLabel = 'Submission';

    protected static ?string $pluralModelLabel = 'Candidate Submissions';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['field', 'submittedByUser'])->latest('submitted_at'))
            ->columns([
                Tables\Columns\TextColumn::make('field.label')
                    ->label('Field')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('submitted_by_type')
                    ->label('Submitted By')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === 'admin') {
                            $name = $record->submittedByUser?->name;

                            return $name ? 'Admin / ' . $name : 'Admin';
                        }

                        return 'Candidate';
                    })
                    ->color(fn ($state) => $state === 'admin' ? 'success' : 'info'),

                Tables\Columns\TextColumn::make('display_value')
                    ->label('Submitted Value')
                    ->state(function ($record) {
                        if (($record->field->field_type ?? null) === 'file') {
                            return filled($record->value) ? 'Open File' : '-';
                        }

                        if (blank($record->value)) {
                            return '-';
                        }

                        return Str::limit((string) $record->value, 80);
                    })
                    ->url(function ($record) {
                        if (($record->field->field_type ?? null) === 'file' && filled($record->value)) {
                            return asset('storage/' . ltrim($record->value, '/'));
                        }

                        return null;
                    })
                    ->openUrlInNewTab()
                    ->color(function ($record) {
                        return ($record->field->field_type ?? null) === 'file' ? 'primary' : null;
                    })
                    ->weight(function ($record) {
                        return ($record->field->field_type ?? null) === 'file' ? 'bold' : null;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('field.field_type')
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
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->bulkActions([]);
    }
}
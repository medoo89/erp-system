<?php

namespace App\Filament\Resources\PreEmployments\Tables;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PreEmploymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('is_archived', false)
                ->where('is_declined', false)
                ->whereNull('declined_at')
                ->whereNull('converted_to_employment_at')
                ->where(function (Builder $stageQuery): void {
                    $stageQuery
                        ->whereNull('status')
                        ->orWhereNotIn('status', [
                            'returned_to_job_application',
                            'converted_to_employment',
                            'declined',
                            'archived',
                        ]);
                })
            )
            ->defaultSort('id', 'desc')
            ->recordUrl(fn ($record) => PreEmploymentResource::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('candidate_name')
                    ->label('Candidate')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'initiated' => 'gray',
                        'under_preparation' => 'warning',
                        'awaiting_candidate_upload' => 'warning',
                        'documents_under_review' => 'info',
                        'additional_documents_required' => 'warning',
                        'pending_medical' => 'warning',
                        'pending_visa' => 'warning',
                        'pending_travel' => 'warning',
                        'ready_for_employment' => 'success',
                        'converted_to_employment' => 'success',
                        'declined' => 'danger',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
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
                        'archived' => 'Archived',
                        default => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-',
                    }),

                Tables\Columns\TextColumn::make('assignedHrUser.name')
                    ->label('Operation Officer')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'view')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'delete')),
                ]),
            ]);
    }
}
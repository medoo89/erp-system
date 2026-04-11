<?php

namespace App\Filament\Resources\Employments\Tables;

use App\Filament\Resources\Employments\EmploymentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class EmploymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordUrl(fn ($record) => EmploymentResource::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'on_hold' => 'warning',
                        'completed' => 'info',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-'),

                Tables\Columns\TextColumn::make('assignedHrUser.name')
                    ->label('Operation Officer')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }
}
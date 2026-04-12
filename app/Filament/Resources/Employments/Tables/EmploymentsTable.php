<?php

namespace App\Filament\Resources\Employments\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class EmploymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position_title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Active',
                        'on_hold' => 'On Hold',
                        'completed' => 'Completed',
                        'terminated' => 'Terminated',
                        default => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-',
                    })
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'on_hold' => 'warning',
                        'completed' => 'info',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('operation_officer_name')
                    ->label('Operation Officer')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('view_record')
                    ->label('')
                    ->state('View')
                    ->color('primary')
                    ->url(fn ($record) => \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordUrl(fn ($record) => \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $record]))
            ->actions([])
            ->bulkActions([]);
    }
}
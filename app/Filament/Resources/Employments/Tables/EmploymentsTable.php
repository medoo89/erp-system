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

                Tables\Columns\TextColumn::make('employee_category')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state ?: 'operational') {
                        'office' => 'Office',
                        'operational' => 'Operational',
                        default => ucfirst(str_replace('_', ' ', (string) $state)),
                    })
                    ->color(fn ($state) => match ($state ?: 'operational') {
                        'office' => 'info',
                        'operational' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('position_title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('office_department')
                    ->label('Department')
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', (string) $state)) : '-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

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

                Tables\Columns\TextColumn::make('contract_type')
                    ->label('Contract')
                    ->formatStateUsing(fn ($state, $record) => ($record->is_open_ended_contract ?? false)
                        ? 'Open-ended'
                        : (filled($state) ? ucfirst(str_replace('_', ' ', (string) $state)) : '-'))
                    ->badge()
                    ->color(fn ($state, $record) => ($record->is_open_ended_contract ?? false) ? 'success' : 'gray')
                    ->toggleable(),

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
                    ->url(fn ($record) => auth()->user()?->canErp('employments', 'view')
                        ? \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $record])
                        : null)
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'view')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('employee_category')
                    ->label('Employee Type')
                    ->options([
                        'operational' => 'Operational',
                        'office' => 'Office',
                    ]),

                Tables\Filters\SelectFilter::make('office_department')
                    ->label('Office Department')
                    ->options([
                        'management' => 'Management',
                        'finance' => 'Finance',
                        'hr' => 'HR',
                        'recruitment' => 'Recruitment',
                        'operations' => 'Operations',
                        'administration' => 'Administration',
                        'sales' => 'Sales',
                        'marketing' => 'Marketing',
                        'it' => 'IT',
                        'other' => 'Other',
                    ]),
            ])
            ->recordUrl(fn ($record) => auth()->user()?->canErp('employments', 'view')
                ? \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $record])
                : null)
            ->actions([])
            ->bulkActions([]);
    }
}
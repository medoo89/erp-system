<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Linked Projects';

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('id', 'desc')
            ->recordUrl(fn ($record): string => ProjectResource::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('project_code')
                    ->label('Project Code')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ((string) $state) {
                        'active' => 'success',
                        'on_hold' => 'warning',
                        'completed', 'closed' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('jobs_count')
                    ->label('Jobs')
                    ->counts('jobs')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) (auth()->user()?->canErp('clients', 'create_project') || auth()->user()?->canErp('projects', 'create')))
                    ->label('Add Project')
                    ->mutateDataUsing(function (array $data): array {
                        $data['client_id'] = $this->ownerRecord->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('projects', 'view'))
                    ->url(fn ($record): string => ProjectResource::getUrl('view', ['record' => $record])),

                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('projects', 'edit'))
                    ->url(fn ($record): string => ProjectResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'view') ?? false);
    }
}

<?php

namespace App\Filament\Resources\ArchivedEmployments;

use App\Filament\Resources\ArchivedEmployments\Pages\ListArchivedEmployments;
use App\Filament\Resources\Employments\EmploymentResource;
use App\Models\Employment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArchivedEmploymentResource extends Resource
{
    protected static ?string $model = Employment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Archived Employment';

    protected static ?string $modelLabel = 'Archived Employment';

    protected static ?string $pluralModelLabel = 'Archived Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 40;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'job.project.client',
            ])
            ->where(function (Builder $query) {
                $query
                    ->whereIn('status', [
                        'inactive',
                        'resigned',
                        'terminated',
                        'archived',
                    ])
                    ->orWhereIn('contract_status', [
                        'expired',
                        'terminated',
                        'closed',
                    ]);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (Employment $record): string => EmploymentResource::getUrl('edit', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('position_title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Employment Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? str_replace('_', ' ', ucwords((string) $state, '_')) : '-')
                    ->color(fn ($state) => match (strtolower((string) $state)) {
                        'inactive' => 'gray',
                        'resigned' => 'warning',
                        'terminated' => 'danger',
                        'archived' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_work_status')
                    ->label('Current Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? str_replace('_', ' ', ucwords((string) $state, '_')) : '-')
                    ->color(fn ($state) => match (strtolower((string) $state)) {
                        'demobilized' => 'warning',
                        'inactive' => 'gray',
                        'terminated' => 'danger',
                        'resigned' => 'warning',
                        'completed' => 'success',
                        'offboarded' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contract_status')
                    ->label('Contract Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? str_replace('_', ' ', ucwords((string) $state, '_')) : '-')
                    ->color(fn ($state) => match (strtolower((string) $state)) {
                        'expired' => 'warning',
                        'terminated' => 'danger',
                        'closed' => 'gray',
                        'active' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contract_end_date')
                    ->label('Contract End Date')
                    ->date('M j, Y')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('demobilization_date')
                    ->label('Demobilization Date')
                    ->date('M j, Y')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                Action::make('restore')
                    ->visible(fn () => (bool) auth()->user()?->canErp('archive', 'restore'))
                    ->label('')
                    ->tooltip('Restore employment')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconButton()
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'sf-archive-row-action sf-archive-row-action-restore',
                        'title' => 'Restore',
                        'aria-label' => 'Restore',
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Restore Archived Employment')
                    ->modalDescription('This employment record will be restored back to the active employment list.')
                    ->modalSubmitActionLabel('Restore')
                    ->action(function (Employment $record): void {
                        $record->forceFill([
                            'status' => 'active',
                            'current_work_status' => filled($record->current_work_status) && $record->current_work_status !== 'inactive'
                                ? $record->current_work_status
                                : 'active',
                            'contract_status' => filled($record->contract_status) && ! in_array($record->contract_status, ['expired', 'terminated', 'closed'], true)
                                ? $record->contract_status
                                : 'active',
                        ])->save();

                        Notification::make()
                            ->title('Employment restored successfully')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('archive', 'delete'))
                    ->label('')
                    ->tooltip('Permanent Delete')
                    ->icon('heroicon-o-trash')
                    ->iconButton()
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'sf-archive-row-action sf-archive-row-action-delete',
                        'title' => 'Permanent Delete',
                        'aria-label' => 'Permanent Delete',
                    ])
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkAction::make('restore_selected')
                    ->visible(fn () => (bool) auth()->user()?->canErp('archive', 'restore'))
                    ->label('Restore Selected')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Restore selected archived employments?')
                    ->modalSubmitActionLabel('Restore Selected')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->forceFill([
                                'status' => 'active',
                                'current_work_status' => filled($record->current_work_status) && $record->current_work_status !== 'inactive'
                                    ? $record->current_work_status
                                    : 'active',
                                'contract_status' => filled($record->contract_status) && ! in_array($record->contract_status, ['expired', 'terminated', 'closed'], true)
                                    ? $record->contract_status
                                    : 'active',
                            ])->save();
                        }

                        Notification::make()
                            ->title('Selected archived employments restored successfully')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_delete')
                    ->visible(fn () => (bool) auth()->user()?->canErp('archive', 'delete'))
                    ->label('Permanent Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete selected employments?')
                    ->modalDescription('This action cannot be undone.')
                    ->modalSubmitActionLabel('Permanent Delete')
                    ->action(function (Collection $records): void {
                        $records->each->delete();

                        Notification::make()
                            ->title('Selected archived employments permanently deleted')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchivedEmployments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('archive', 'view') ?? false);
    }
}

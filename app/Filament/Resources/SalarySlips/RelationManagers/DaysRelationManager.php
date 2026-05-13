<?php

namespace App\Filament\Resources\SalarySlips\RelationManagers;

use App\Models\SalarySlipDay;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DaysRelationManager extends RelationManager
{
    protected static string $relationship = 'days';

    protected static ?string $title = 'Salary Slip Days';

    protected static ?string $modelLabel = 'Salary Slip Day';

    protected static ?string $pluralModelLabel = 'Salary Slip Days';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('attendance_status')
                    ->label('Attendance Status')
                    ->options(SalarySlipDay::statusLabels())
                    ->required()
                    ->native(false)
                    ->live(),

                Toggle::make('is_paid_day')
                    ->label('Paid Day')
                    ->default(true),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('work_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('work_date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('day_name')
                    ->label('Day')
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SalarySlipDay::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        SalarySlipDay::STATUS_PRESENT => 'success',
                        SalarySlipDay::STATUS_ABSENT => 'danger',
                        SalarySlipDay::STATUS_SICK => 'warning',
                        SalarySlipDay::STATUS_LEAVE => 'info',
                        SalarySlipDay::STATUS_UNPAID_LEAVE => 'danger',
                        SalarySlipDay::STATUS_HOLIDAY => 'gray',
                        SalarySlipDay::STATUS_TRAVEL => 'primary',
                        SalarySlipDay::STATUS_OTHER => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_paid_day')
                    ->label('Paid')
                    ->boolean(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->wrap()
                    ->default('-'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'edit')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'delete')),
                ]),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }
}
